<?php

namespace App\Mail; // Define el espacio de nombres para esta clase Mailable.

use App\Models\Superappointment; // Importa el modelo Superappointment, que contiene los datos de la cita.
use Illuminate\Bus\Queueable; // Permite que este Mailable se pueda encolar para envío asíncrono, mejorando el rendimiento.
use Illuminate\Mail\Mailable; // Clase base para todos los Mailables en Laravel.
use Illuminate\Mail\Mailables\Content; // Se usa para definir el contenido del correo (la vista y los datos que se le pasan).
use Illuminate\Mail\Mailables\Envelope; // Se usa para definir el sobre del correo (remitente, destinatario, asunto).
use Illuminate\Queue\SerializesModels; // Permite que los modelos Eloquent (como Superappointment) se serialicen correctamente si el Mailable se encola.
use Illuminate\Mail\Mailables\Address; // Se usa para crear objetos de dirección de correo electrónico de forma estructurada.

class NewSuperAppointmentNotificationToAdmin extends Mailable // Define la clase del Mailable.
// Nota: El comentario original "No implementa ShouldQueue" indica que, por defecto, este correo se enviaría de forma síncrona.
// Sin embargo, el uso de `use Queueable;` sugiere que sí está preparado para ser encolado si se desea.
// Si se quiere forzar el envío síncrono, se omitiría `implements ShouldQueue` si se estuviera usando.
{
    use Queueable, SerializesModels; // Habilita la funcionalidad de encolamiento y serialización de modelos.

    public Superappointment $superappointment; // Propiedad pública para almacenar la instancia del modelo Superappointment.
                                            // Laravel automáticamente hace disponibles las propiedades públicas a la vista del correo.

    /**
     * Crea una nueva instancia del mensaje.
     *
     * @param Superappointment $superappointment La instancia del modelo Superappointment con los datos de la cita.
     */
    public function __construct(Superappointment $superappointment)
    {
        // Asigna la instancia de Superappointment recibida a la propiedad pública de la clase.
        $this->superappointment = $superappointment;
    }

    /**
     * Obtiene la definición del sobre del mensaje.
     * Define quién envía el correo, a quién va dirigido y cuál es el asunto.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // Define el remitente del correo.
            // Utiliza variables de entorno para la dirección y el nombre, con valores por defecto.
            from: new Address(env('MAIL_FROM_ADDRESS', 'sistema@fotovalera.com'), env('MAIL_FROM_NAME', 'Sistema Fotovalera')),
            // Define el destinatario del correo.
            // Utiliza una variable de entorno para la dirección del administrador, con un valor por defecto.
            to: env('ADMIN_APPOINTMENT_NOTIFICATION_EMAIL', 'infofotovalera@gmail.com'),
            // Define el asunto del correo.
            // Incluye el nombre del servicio principal de la cita para una rápida identificación.
            subject: 'Nueva Solicitud de Cita (Super): ' . $this->superappointment->primary_service_name,
        );
    }

    /**
     * Obtiene la definición del contenido del mensaje.
     * Especifica la vista Markdown que se usará para el cuerpo del correo y los datos que se le pasarán.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        // Procesa la lista de servicios adicionales para mostrarla como una cadena separada por comas.
        // Si no hay servicios adicionales, muestra "Ninguno".
        $additionalServicesString = !empty($this->superappointment->additional_services)
            ? implode(', ', $this->superappointment->additional_services) // Une los servicios con una coma.
            : 'Ninguno'; // Texto por defecto si no hay servicios adicionales.

        return new Content(
            // Especifica la vista Markdown que se usará para el contenido del correo.
            // Laravel buscará este archivo en 'resources/views/emails/superappointments/notification-admin.blade.php'.
            markdown: 'emails.superappointments.notification-admin',
            // Pasa un array de datos a la vista. Las claves de este array estarán disponibles como variables en la vista.
            with: [
                // Nombre del cliente: usa guest_name, luego el nombre del usuario asociado, o 'N/A' si ninguno está disponible.
                'clientName' => $this->superappointment->guest_name ?? $this->superappointment->user?->name ?? 'N/A',
                'clientEmail' => $this->superappointment->email, // Email del cliente.
                'clientPhone' => $this->superappointment->phone ?? 'N/A', // Teléfono del cliente, o 'N/A' si no se proporcionó.
                'primaryServiceName' => $this->superappointment->primary_service_name, // Nombre del servicio principal.
                'additionalServices' => $additionalServicesString, // Cadena de servicios adicionales.
                // Formatea la fecha y hora de la cita para que sea legible.
                'appointmentDateTime' => $this->superappointment->appointment_datetime->format('d/m/Y \a \l\a\s H:i'),
                'appointmentNotes' => $this->superappointment->notes, // Notas adicionales de la cita.
                'appointmentId' => $this->superappointment->id, // ID de la cita, útil para enlaces o referencias.
            ],
        );
    }

    /**
     * Obtiene los adjuntos para el mensaje.
     * En este caso, no hay archivos adjuntos.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return []; // Devuelve un array vacío, indicando que no hay adjuntos.
    }
}
