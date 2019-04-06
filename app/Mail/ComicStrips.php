<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ComicStrips extends Mailable
{
    use Queueable, SerializesModels;

    protected $comicStrips;

    protected $logEntries;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($comicStrips, $logEntries)
    {
        $this->comicStrips = $comicStrips;
        $this->logEntries = $logEntries;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__(
                'Your Sammaye\'s Comics Feed for :date',
                ['date' => date('d-m-Y')]
            ))
            ->view('mail.comic.comicStrips')
            ->with([
                'comicStrips' => $this->comicStrips,
                'logEntries' => $this->logEntries,
            ]);
    }
}
