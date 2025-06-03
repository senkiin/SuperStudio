<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class AppointmentConfirmationToUser extends Mailable 
{
    use Queueable, SerializesModels;

    public Appointment $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS', 'infofotovalera@gmail.com'), env('MAIL_FROM_NAME', 'Fotovalera')),
            to: $this->appointment->email,
            subject: 'Confirmación de tu Solicitud de Cita en Fotovalera',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointments.confirmation-user',
            with: [
                'appointmentName' => $this->appointment->guest_name ?? $this->appointment->user?->name ?? 'Cliente',
                'serviceName' => $this->appointment->serviceType->name,
                'appointmentDateTime' => $this->appointment->appointment_datetime->format('d/m/Y \a \l\a\s H:i'),
                'appointmentNotes' => $this->appointment->notes,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
