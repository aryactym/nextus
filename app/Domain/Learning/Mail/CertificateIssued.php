<?php

declare(strict_types=1);

namespace App\Domain\Learning\Mail;

use App\Domain\Learning\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateIssued extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Certificate $certificate) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Sertifikat penyelesaian terbit');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.certificate');
    }
}
