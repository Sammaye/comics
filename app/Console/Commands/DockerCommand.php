<?php

namespace App\Console\Commands;

use Exception;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Console\Command;

class DockerCommand extends Command
{
    public $name = 'DockerCommand';

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
            $client = self::getGoogleClient();
            $this->googleService = new Google_Service_Drive($client);
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
        $client->setApplicationName('Google Drive API PHP Quickstart');
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

        if (count($results->getFiles()) == 0) {
            return false;
        } else {
            foreach ($results->getFiles() as $file) {
                /** @var Google_Service_Drive_DriveFile $file */
                if ($file->getName() === $name) {
                    $fileContent = $this->googleService->files->get($file->getId(), ['alt' => 'media']);
                    file_put_contents(storage_path($name), $fileContent->getBody());
                    return $file;
                }
            }
        }
    }
}
