<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class comicRequest extends Mailable
{
    use Queueable, SerializesModels;

    protected $theme = 'blank';

    protected $vars = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($vars)
    {
        $this->vars = $vars;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('New Comic Request'))
            ->markdown('mail.comic.request')
            ->with($this->vars);
    }
}
