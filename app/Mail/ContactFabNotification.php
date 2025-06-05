<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Carbon\Carbon; // Importar Carbon

class ContactFabNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $contactName;
    public string $contactEmail;
    public string $contactPhone;    // Nuevo
    public string $fixedCategory;   // Cambio de nombre/propósito
    public string $contactDescription;
    public Carbon $requestDateTime; // Nuevo

    public function __construct(
        string $name,
        string $email,
        string $phone, // Nuevo
        string $category, // Ahora es la categoría fija
        string $description,
        Carbon $dateTime // Nuevo
    )
    {
        $this->contactName = $name;
        $this->contactEmail = $email;
        $this->contactPhone = $phone;
        $this->fixedCategory = $category;
        $this->contactDescription = $description;
        $this->requestDateTime = $dateTime;
    }

    public function envelope(): Envelope
    {
        $fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@example.com');
        $fromName = env('MAIL_FROM_NAME', config('app.name'));

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            replyTo: [new Address($this->contactEmail, $this->contactName)], // Importante para responder al cliente
            subject: 'Nueva Solicitud: ' . $this->fixedCategory . ' de ' . $this->contactName, // Asunto actualizado
        );
    }

    public function content(): Content
    {
        // Asegúrate de que la vista 'emails.contact.fab-notification' esté actualizada
        // para mostrar el teléfono y la fecha/hora.
        return new Content(
            // Asumo que estás usando una vista HTML personalizada
            view: 'emails.contact.fab-notification', // O markdown si prefieres y ajustas la vista
            with: [
                'name' => $this->contactName,
                'email' => $this->contactEmail,
                'phone' => $this->contactPhone,
                'category' => $this->fixedCategory, // Nombre de variable consistente con la vista
                'description' => $this->contactDescription,
                'dateTime' => $this->requestDateTime->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i T'), // Formato legible
                'appName' => config('app.name'), // Para el footer del email, si es necesario
                // 'logoUrl' => ... // Si tu plantilla de email usa un logo
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
