<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SystemNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $title;
    public string $body;

    /**
     * Create a new message instance.
     */
    public function __construct(string $title, string $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject($this->title)
            ->view('emails.system-notification');
    }
}

