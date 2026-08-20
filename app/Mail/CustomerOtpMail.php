<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerOtpMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  string  $customerName  Recipient's name.
     * @param  string  $otpCode  Six-digit verification code.
     * @param  int  $expiresInMinutes  How long the code stays valid.
     */
    public function __construct(
        public string $customerName,
        public string $otpCode,
        public int $expiresInMinutes = 10,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Venecia — '.__('account verification code'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.customer-otp');
    }
}
