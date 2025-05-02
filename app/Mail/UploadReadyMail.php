<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UploadReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $downloadLink;

    /**
     * Create a new message instance.
     */
    public function __construct($downloadLink)
    {
        $this->downloadLink = $downloadLink;
    }

    public function build()
    {
        return $this->subject('Your File is Ready to Download')
            ->markdown('emails.upload_mail')
            ->with([
                'downloadLink' => $this->downloadLink,
            ]);
    }
}
