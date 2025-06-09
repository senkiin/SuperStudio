<?php

namespace App\Mail; // Define el espacio de nombres para esta clase Mailable.

use Illuminate\Bus\Queueable; // Permite que este Mailable se pueda encolar para envío asíncrono.
use Illuminate\Mail\Mailable; // Clase base para todos los Mailables.
use Illuminate\Mail\Mailables\Content; // Usado para definir el contenido del correo (vista, datos).
use Illuminate\Mail\Mailables\Envelope; // Usado para definir el sobre del correo (remitente, destinatario, asunto).
use Illuminate\Queue\SerializesModels; // Permite que los modelos Eloquent se serialicen correctamente si el Mailable se encola.
use Illuminate\Mail\Mailables\Address; // Usado para crear objetos de dirección de correo electrónico.
use Carbon\Carbon; // Importa la clase Carbon para manejar fechas y horas de forma más sencilla.

class ContactFabNotification extends Mailable
{
    use Queueable, SerializesModels; // Utiliza los traits para encolamiento y serialización.

    // --- Propiedades Públicas ---
    // Estas propiedades almacenan los datos que se pasarán a la vista del correo electrónico.
    // Son públicas para que Laravel pueda acceder a ellas al renderizar la vista.

    public string $contactName;        // Nombre de la persona que contacta.
    public string $contactEmail;       // Email de la persona que contacta.
    public string $contactPhone;       // Teléfono de la persona que contacta (nuevo).
    public string $fixedCategory;      // Categoría o propósito fijo del contacto (ej. "Solicitar información").
    public string $contactDescription; // El mensaje o descripción proporcionado por el contacto.
    public Carbon $requestDateTime;    // La fecha y hora en que se realizó la solicitud de contacto (nuevo).

    /**
     * Crea una nueva instancia del mensaje.
     *
     * El constructor recibe los datos necesarios para el correo y los asigna a las propiedades públicas.
     *
     * @param string $name Nombre del contacto.
     * @param string $email Email del contacto.
     * @param string $phone Teléfono del contacto.
     * @param string $category Categoría del contacto.
     * @param string $description Descripción del contacto.
     * @param Carbon $dateTime Fecha y hora de la solicitud.
     */
    public function __construct(
        string $name,
        string $email,
        string $phone, // Nuevo parámetro para el teléfono.
        string $category, // Ahora representa la categoría fija.
        string $description,
        Carbon $dateTime // Nuevo parámetro para la fecha y hora.
    )
    {
        // Asigna los valores recibidos a las propiedades de la clase.
        $this->contactName = $name;
        $this->contactEmail = $email;
        $this->contactPhone = $phone;
        $this->fixedCategory = $category;
        $this->contactDescription = $description;
        $this->requestDateTime = $dateTime;
    }

    /**
     * Obtiene la definición del sobre del mensaje.
     *
     * Define el remitente (from), el destinatario (to), la dirección de respuesta (replyTo)
     * y el asunto (subject) del correo electrónico.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        // Obtiene la dirección de correo del remitente desde las variables de entorno o usa un valor por defecto.
        $fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@example.com');
        // Obtiene el nombre del remitente desde las variables de entorno o el nombre de la aplicación.
        $fromName = env('MAIL_FROM_NAME', config('app.name'));

        return new Envelope(
            from: new Address($fromEmail, $fromName), // Establece el remitente.
            // Establece la dirección de respuesta al email del contacto,
            // para que al responder al correo de notificación, se responda directamente al cliente.
            replyTo: [new Address($this->contactEmail, $this->contactName)],
            // Asunto del correo, ahora incluye la categoría y el nombre del contacto.
            subject: 'Nueva Solicitud: ' . $this->fixedCategory . ' de ' . $this->contactName,
        );
    }

    /**
     * Obtiene la definición del contenido del mensaje.
     *
     * Especifica la vista Blade que se usará para el cuerpo del correo y los datos
     * que se pasarán a esa vista.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        // Asegúrate de que la vista 'emails.contact.fab-notification' esté actualizada
        // para mostrar el teléfono y la fecha/hora.
        return new Content(
            // Especifica la vista Blade que se usará para el contenido del correo.
            // Puede ser una vista HTML (view) o Markdown (markdown).
            view: 'emails.contact.fab-notification',
            // Pasa los datos a la vista. Las claves del array estarán disponibles como variables en la vista.
            with: [
                'name' => $this->contactName,
                'email' => $this->contactEmail,
                'phone' => $this->contactPhone, // Pasa el teléfono a la vista.
                'category' => $this->fixedCategory, // Nombre de variable consistente para la categoría.
                'description' => $this->contactDescription,
                // Formatea la fecha y hora para que sea legible, incluyendo la zona horaria.
                // 'translatedFormat' usa la localización de Carbon para nombres de meses/días si está configurada.
                'dateTime' => $this->requestDateTime->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i T'),
                'appName' => config('app.name'), // Nombre de la aplicación, útil para el pie de página del correo.
                // 'logoUrl' => ... // Si tu plantilla de email usa un logo, puedes pasar su URL aquí.
            ],
        );
    }

    /**
     * Obtiene los adjuntos para el mensaje.
     *
     * En este caso, no hay adjuntos, por lo que devuelve un array vacío.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return []; // No hay archivos adjuntos.
    }
}
