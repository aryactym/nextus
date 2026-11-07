<?php

declare(strict_types=1);

namespace App\Domain\Learning\Mail;

use App\Domain\Learning\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enrollment $enrollment) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Pendaftaran kursus berhasil');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.enrollment');
    }
}
