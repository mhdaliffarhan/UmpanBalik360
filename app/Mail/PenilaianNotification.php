<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class PenilaianNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $penilaianDetails;

    /**
     * Create a new message instance.
     */
    public function __construct($penilaianDetails)
    {
        $this->penilaianDetails = $penilaianDetails;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('mhdaliffarhan22@gmail.com', 'UmpanBalik360'),
            subject: $this->penilaianDetails['nama_penilaian'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.penilaian',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
