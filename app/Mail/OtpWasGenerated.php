<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpWasGenerated extends Mailable
{
    use Queueable, SerializesModels;

    protected string $token;

    /**
     * Create a new message instance.
     */
    public function __construct(string $token)
    {
       $this->token = $token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('cookbookshq@gmail.com', 'CookbooksHQ'),
            subject: 'Your OTP',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.send-otp',
            with: [
                'token' => $this->token
            ]
        );
    }
}
