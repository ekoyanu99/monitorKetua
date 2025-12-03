<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServerDownMail extends Mailable
{
    use Queueable, SerializesModels;

    public $monitorName;
    public $component;
    public $errorMessage;

    public function __construct($monitorName, $component, $errorMessage)
    {
        $this->monitorName = $monitorName;
        $this->component = $component;
        $this->errorMessage = $errorMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ALERT: Server Down - ' . $this->monitorName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.server_down',
        );
    }
}
