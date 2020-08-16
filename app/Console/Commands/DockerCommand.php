<?php

namespace App\Console\Commands;

use Exception;
use Google_Client;
use Google_Http_MediaFileUpload;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class DockerCommand extends Command
{
    public $name = 'DockerCommand';

    /**
     * @var Google_Client|null
     */
    public $googleClient;

    /**
     * @var Google_Service_Drive|null
     */
    public $googleService;

    /**
     * @return Google_Service_Drive
     * @throws \Google_Exception
     */
    public function getGoogleService()
    {
        if ($this->googleService === null) {
            $this->googleClient = self::getGoogleClient();
            $this->googleService = new Google_Service_Drive($this->googleClient);
        }
        return $this->googleService;
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     * @throws \Google_Exception
     */
    public function getGoogleClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Comics App');
        $client->setScopes(Google_Service_Drive::DRIVE);
        $client->setAuthConfig(base_path('config/credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = base_path('config/token.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                $this->line(sprintf("Open the following link in your browser:\n%s\n", $authUrl));
                $authCode = trim($this->ask('Enter verification code:'));

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }

    /**
     * @param $name
     * @param $data
     * @param string $mimeType
     * @return mixed
     * @throws \Google_Exception
     */
    public function upsertGoogleDriveFile($name, $data, $mimeType = 'text/plain')
    {
        $this->getGoogleService();

        $optParams = array(
            'pageSize' => 10,
            'fields' => 'nextPageToken, files(id, name, trashed)',
            'q' => "name = '"  . $name . "' and trashed = false",
        );
        $results = $this->googleService->files->listFiles($optParams);

        $drive_file = new Google_Service_Drive_DriveFile();
        $drive_file->setName($name);

        $opts = ['data' => $data, 'mimeType' => $mimeType, 'uploadType' => 'resumable'];

        if (count($results->getFiles()) == 0) {
            $file = $this->googleService->files->create($drive_file, $opts);
            return $file->getId();
        } else {
            foreach ($results->getFiles() as $file) {
                if ($file->getName() === $name) {
                    $this->googleService->files->update($file->getId(), $drive_file, $opts);
                    return $file->getId();
                }
            }
        }

        return false;
    }

    /**
     * @param $name
     * @param $filePath
     * @param string $mimeType
     * @return mixed
     * @throws \Google_Exception
     */
    public function upsertLargeGoogleDriveFile($name, $filePath, $mimeType = 'text/plain')
    {
        $this->getGoogleService();

        $optParams = array(
            'pageSize' => 10,
            'fields' => 'nextPageToken, files(id, name, trashed)',
            'q' => "name = '"  . $name . "' and trashed = false",
        );
        $results = $this->googleService->files->listFiles($optParams);

        $this->googleClient->setDefer(true);

        $drive_file = new Google_Service_Drive_DriveFile();
        $drive_file->setName($name);

        $chunkSizeBytes = 100 * 1024 * 1024;
        $filesize = filesize($filePath);
        $chunkCount = $filesize/$chunkSizeBytes;

        $bar = $this->output->createProgressBar(ceil($chunkCount));
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        if (count($results->getFiles()) == 0) {
            $this->info(__('Creating file...'));

            $request = $this->googleService->files->create($drive_file);
        } else {
            foreach ($results->getFiles() as $file) {
                if ($file->getName() === $name) {
                    $id =  $file->getId();

                    $this->info(__('Updating file :id...', ['id' => $id]));

                    $request = $this->googleService->files->update($id, $drive_file);
                }
            }
        }

        $media = new Google_Http_MediaFileUpload(
            $this->googleClient,
            $request,
            $mimeType,
            null,
            true,
            $chunkSizeBytes
        );
        $media->setFileSize($filesize);

        $this->info(__('Starting upload...'));

        // Upload the various chunks. $status will be false until the process is
        // complete.
        $status = false;
        $handle = fopen($filePath, "rb");
        while (!$status && !feof($handle)) {
            $chunk = $this->readFileChunk($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
            $bar->advance();
        }

        // The final value of $status will be the data from the API for the object
        // that has been uploaded.
        $result = false;
        if ($status != false) {
            $result = $status;
        }

        fclose($handle);

        if ($result instanceof Google_Service_Drive_DriveFile) {
            return $result->getId();
        }

        return false;
    }

    private function readFileChunk ($handle, $chunkSize)
    {
        $byteCount = 0;
        $giantChunk = "";
        while (!feof($handle)) {
            // fread will never return more than 8192 bytes if the stream
            // is read buffered and it does not represent a plain file
            $chunk = fread($handle, 8192);
            $byteCount += strlen($chunk);
            $giantChunk .= $chunk;
            if ($byteCount >= $chunkSize)
            {
                return $giantChunk;
            }
        }
        return $giantChunk;
    }

    /**
     * @param $name
     * @return false|Google_Service_Drive_DriveFile
     * @throws \Google_Exception
     */
    public function getGoogleDriveFile($name)
    {
        $this->getGoogleService();

        $optParams = array(
            'pageSize' => 10,
            'fields' => 'nextPageToken, files(id, name, trashed, webContentLink)',
            'q' => "name = '"  . $name . "' and trashed = false",
        );
        $results = $this->googleService->files->listFiles($optParams);

        if (count($results->getFiles()) !== 0) {
            foreach ($results->getFiles() as $file) {
                /** @var Google_Service_Drive_DriveFile $file */
                if ($file->getName() === $name) {
                    $fileContent = $this->googleService->files->get($file->getId(), ['alt' => 'media']);
                    file_put_contents(storage_path($name), $fileContent->getBody());
                    return $file;
                }
            }
        }
        return false;
    }

    /**
     * @param $name
     * @return false|Google_Service_Drive_DriveFile
     * @throws \Google_Exception
     */
    public function getLargeGoogleDriveFile($name)
    {
        $this->getGoogleService();

        $filePath = storage_path($name);

        $optParams = array(
            'pageSize' => 10,
            'fields' => 'nextPageToken, files(id, name, trashed, webContentLink, size)',
            'q' => "name = '"  . $name . "' and trashed = false",
        );
        $results = $this->googleService->files->listFiles($optParams);

        if (count($results->getFiles()) !== 0) {
            foreach ($results->getFiles() as $file) {
                /** @var Google_Service_Drive_DriveFile $file */
                if ($file->getName() === $name) {
                    $fileId = $file->getId();
                    $fileSize = $file->getSize();

                    $this->info(__('Downloading file :id...', ['id' => $fileId]));

                    // Get the authorized Guzzle HTTP client
                    $http = $this->googleClient->authorize();

                    // Open a file for writing
                    $fp = fopen($filePath, 'w');

                    // Download in 100 MB chunks
                    $chunkSizeBytes = 100 * 1024 * 1024;
                    $chunkStart = 0;

                    $chunkCount = ceil($fileSize/$chunkSizeBytes);

                    $bar = $this->output->createProgressBar($chunkCount);
                    $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

                    // Iterate over each chunk and write it to our file
                    while ($chunkStart < $fileSize) {
                        $chunkEnd = $chunkStart + $chunkSizeBytes;
                        $response = $http->request(
                            'GET',
                            sprintf('/drive/v3/files/%s', $fileId),
                            [
                                'query' => ['alt' => 'media'],
                                'headers' => [
                                    'Range' => sprintf('bytes=%s-%s', $chunkStart, $chunkEnd)
                                ]
                            ]
                        );
                        $chunkStart = $chunkEnd + 1;
                        fwrite($fp, $response->getBody()->getContents());

                        $bar->advance();
                    }
                    // close the file pointer
                    fclose($fp);

                    return $file;
                }
            }
        }

        return false;
    }
}
