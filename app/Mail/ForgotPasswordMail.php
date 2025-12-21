<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;

class ForgotPasswordMail extends Mailable
{
    public $mailData;

    // A
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    // B
    public function envelope()
    {
        return new Envelope(
            from: new Address(
                env('MAIL_FROM_ADDRESS'),
                env('MAIL_FROM_NAME')
            ),
            subject: 'Reset your password',
        );
    }

    // C
    public function content()
    {
        return new Content(
            view: 'emails.forgotPassword',
        );
    }
}
