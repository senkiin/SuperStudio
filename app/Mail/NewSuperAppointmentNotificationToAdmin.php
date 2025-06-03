<?php

namespace App\Mail;

use App\Models\Superappointment; // Usa el nuevo modelo
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class NewSuperAppointmentNotificationToAdmin extends Mailable // No implementa ShouldQueue
{
    use Queueable, SerializesModels;

    public Superappointment $superappointment;

    /**
     * Create a new message instance.
     */
    public function __construct(Superappointment $superappointment)
    {
        $this->superappointment = $superappointment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS', 'sistema@fotovalera.com'), env('MAIL_FROM_NAME', 'Sistema Fotovalera')),
            to: env('ADMIN_APPOINTMENT_NOTIFICATION_EMAIL', 'infofotovalera@gmail.com'),
            subject: 'Nueva Solicitud de Cita (Super): ' . $this->superappointment->primary_service_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $additionalServicesString = !empty($this->superappointment->additional_services)
            ? implode(', ', $this->superappointment->additional_services)
            : 'Ninguno';

        return new Content(
            markdown: 'emails.superappointments.notification-admin',
            with: [
                'clientName' => $this->superappointment->guest_name ?? $this->superappointment->user?->name ?? 'N/A',
                'clientEmail' => $this->superappointment->email,
                'clientPhone' => $this->superappointment->phone ?? 'N/A',
                'primaryServiceName' => $this->superappointment->primary_service_name,
                'additionalServices' => $additionalServicesString,
                'appointmentDateTime' => $this->superappointment->appointment_datetime->format('d/m/Y \a \l\a\s H:i'),
                'appointmentNotes' => $this->superappointment->notes,
                'appointmentId' => $this->superappointment->id,
            ],
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
