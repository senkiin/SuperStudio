<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class NewAppointmentNotificationToAdmin extends Mailable
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
            from: new Address(env('MAIL_FROM_ADDRESS', 'sistema@fotovalera.com'), env('MAIL_FROM_NAME', 'Sistema Fotovalera')),
            to: env('ADMIN_APPOINTMENT_NOTIFICATION_EMAIL', 'infofotovalera@gmail.com'),
            subject: 'Nueva Solicitud de Cita: ' . $this->appointment->serviceType->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointments.notification-admin',
            with: [
                'clientName' => $this->appointment->guest_name ?? $this->appointment->user?->name ?? 'N/A',
                'clientEmail' => $this->appointment->email,
                'clientPhone' => $this->appointment->phone ?? 'N/A',
                'serviceName' => $this->appointment->serviceType->name,
                'appointmentDateTime' => $this->appointment->appointment_datetime->format('d/m/Y \a \l\a\s H:i'),
                'appointmentNotes' => $this->appointment->notes,
                'appointmentId' => $this->appointment->id,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
