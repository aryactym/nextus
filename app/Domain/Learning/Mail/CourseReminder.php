<?php

declare(strict_types=1);

namespace App\Domain\Learning\Mail;

use App\Domain\Learning\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enrollment $enrollment) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Lanjutkan kursus Anda di '.config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reminder');
    }
}
