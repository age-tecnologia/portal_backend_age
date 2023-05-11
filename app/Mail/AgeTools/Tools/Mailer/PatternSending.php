<?php

namespace App\Mail\AgeTools\Tools\Mailer;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PatternSending extends Mailable
{
    use Queueable, SerializesModels;

    private $htmlTemplate;
    private string $title;
    private $variables;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $html, $variables)
    {
        $this->title = $title;
        $this->html = $html;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.template',
            with: ['html' => $this->htmlTemplate]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
