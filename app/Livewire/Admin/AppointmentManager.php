<?php

namespace App\Livewire\Admin;

use App\Models\Superappointment; // Asegúrate de que el namespace de tu modelo es correcto
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

// Define la clase del componente Livewire para gestionar citas en el panel de administración.
class AppointmentManager extends Component // O AdminAppointmentManager si así se llama tu clase/archivo
{
    use WithPagination; // Habilita la paginación para las listas de citas.

    public $selectedAppointment; // Almacena la cita seleccionada para ver detalles o editar.
    public $showModal = false; // Controla la visibilidad del modal de detalles de la cita.
    public $showEmailModal = false; // Controla la visibilidad del modal para enviar correos.

    // Propiedades para el formulario de envío de correo electrónico.
    public $emailTo; // Destinatario del correo.
    public $emailSubject; // Asunto del correo.
    public $emailBody; // Cuerpo del correo.

    public $searchTerm = ''; // Término de búsqueda para filtrar citas.
    public $statusFilter = ''; // Filtro para mostrar citas por estado (pending, confirmed, etc.).

    public $notificationMessage = ''; // Mensaje para notificaciones flash (éxito, error).
    public $notificationType = ''; // Tipo de notificación ('success', 'error').

    // Define las reglas de validación para el formulario de correo electrónico.
    protected function rules()
    {
        return [
            'emailTo' => 'required|email', // El destinatario es obligatorio y debe ser un email válido.
            'emailSubject' => 'required|string|min:3|max:255', // El asunto es obligatorio, mínimo 3 caracteres, máximo 255.
            'emailBody' => 'required|string|min:10|max:5000', // El cuerpo es obligatorio, mínimo 10 caracteres, máximo 5000.
        ];
    }

    // Define mensajes de error personalizados para la validación del formulario de correo.
    protected function messages()
    {
        return [
            'emailTo.required' => 'El destinatario del correo es obligatorio.',
            'emailTo.email' => 'El formato del correo del destinatario no es válido.',
            'emailSubject.required' => 'El asunto del correo es obligatorio.',
            'emailSubject.min' => 'El asunto debe tener al menos 3 caracteres.',
            'emailBody.required' => 'El cuerpo del correo es obligatorio.',
            'emailBody.min' => 'El cuerpo del correo debe tener al menos 10 caracteres.',
        ];
    }

    // Hook de Livewire: se ejecuta cuando la propiedad $searchTerm se actualiza.
    // Utilizado para resetear la paginación cuando el usuario busca.
    public function updatingSearchTerm()
    {
        $this->resetPage(); // Resetea la paginación al buscar.
    }

    // Hook de Livewire: se ejecuta cuando la propiedad $statusFilter se actualiza.
    // Utilizado para resetear la paginación cuando el usuario filtra por estado.
    public function updatingStatusFilter()
    {
        $this->resetPage(); // Resetea la paginación al filtrar.
    }

    // Abre el modal para mostrar los detalles de una cita específica.
    public function openAppointmentModal(int $appointmentId)
    {
        $this->selectedAppointment = Superappointment::find($appointmentId); // Busca la cita por ID.
        if ($this->selectedAppointment) {
            $this->showModal = true; // Muestra el modal de detalles.
            $this->showEmailModal = false; // Asegura que el modal de email esté oculto.
            $this->resetNotification(); // Limpia notificaciones previas.
        }
    }

    // Cierra todos los modales abiertos y resetea propiedades relacionadas.
    public function closeModals()
    {
        $this->showModal = false;
        $this->showEmailModal = false;
        $this->selectedAppointment = null; // Limpia la cita seleccionada.
        $this->reset(['emailTo', 'emailSubject', 'emailBody']); // Resetea los campos del formulario de email.
        $this->resetErrorBag(); // Limpia los errores de validación del formulario de email.
    }

    // Abre el modal para enviar un correo electrónico.
    // Puede precargar datos si se proporciona un ID de cita.
    public function openEmailModal(int $appointmentId = null)
    {
        $this->resetErrorBag(); // Limpia errores de validación previos.
        if ($appointmentId) {
            $appointment = Superappointment::find($appointmentId);
            if ($appointment) {
                $this->selectedAppointment = $appointment;
                $this->emailTo = $appointment->email; // Precarga el email del cliente.
                $this->emailSubject = "Respuesta a tu cita para " . $appointment->primary_service_name; // Asunto por defecto.
                // Formatea la fecha y hora de la cita usando Carbon para un formato legible.
                $appointmentDateTime = Carbon::parse($appointment->appointment_datetime);
                // Cuerpo del email por defecto, incluyendo detalles de la cita.
                $this->emailBody = "Hola " . $appointment->guest_name . ",\n\n" .
                                   "Con respecto a tu cita programada para el " .
                                   $appointmentDateTime->translatedFormat('l, d \d\e F \d\e Y \a \l\a\s H:i') . // Formato localizado (ej: lunes, 10 de enero de 2024 a las 10:00).
                                   " (" . $appointmentDateTime->diffForHumans() . ") para el servicio '" . // Tiempo relativo (ej: hace 2 días, en 3 horas).
                                   $appointment->primary_service_name . "':\n\n";
            }
        } else {
            $this->selectedAppointment = null; // Si no hay ID, es para un correo general (no usado en la UI actual).
            $this->reset(['emailTo', 'emailSubject', 'emailBody']); // Resetea campos de email.
        }
        $this->showModal = false; // Asegura que el modal de detalles esté oculto.
        $this->showEmailModal = true; // Muestra el modal de email.
        $this->resetNotification(); // Limpia notificaciones previas.
    }

    // Envía el correo electrónico personalizado redactado por el administrador.
    public function sendCustomEmail()
    {
        $this->validate(); // Valida los campos del formulario de email usando las $rules y $messages.
        $this->resetNotification(); // Limpia notificaciones previas.

        try {
            // Envía un correo de texto plano.
            Mail::raw($this->emailBody, function ($message) {
                $message->to($this->emailTo)
                        ->subject($this->emailSubject)
                        // Usa la dirección de remitente configurada en .env o config/mail.php.
                        ->from(env('MAIL_FROM_ADDRESS', config('mail.from.address')), env('MAIL_FROM_NAME', config('mail.from.name')));
            });

            $this->setNotification('Correo enviado con éxito a ' . $this->emailTo . '.', 'success'); // Muestra mensaje de éxito.
            $this->closeModals(); // Cierra el modal de email.

        } catch (\Exception $e) {
            // Registra el error para depuración.
            Log::error('Error al enviar correo personalizado por admin: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->setNotification('Error al enviar el correo. Por favor, inténtalo más tarde o revisa la configuración.', 'error'); // Muestra mensaje de error.
        }
    }

    // Actualiza el estado de una cita específica.
    public function updateAppointmentStatus(int $appointmentId, string $newStatus)
    {
        $appointment = Superappointment::find($appointmentId);
        if ($appointment) {
            $allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed']; // Estados permitidos.
            if (in_array($newStatus, $allowedStatuses)) {
                $appointment->status = $newStatus;
                $appointment->save(); // Guarda el cambio en la base de datos.
                $this->setNotification('Estado de la cita ID ' . $appointment->id . ' actualizado a: ' . ucfirst($newStatus) . '.', 'success');

                // Lógica opcional para notificar al cliente por correo sobre el cambio de estado.
                // Ejemplo:
                // if ($newStatus === 'confirmed' && $appointment->email) {
                //     Mail::to($appointment->email)->send(new YourAppointmentConfirmedMailable($appointment));
                // } elseif ($newStatus === 'cancelled' && $appointment->email) {
                //     Mail::to($appointment->email)->send(new YourAppointmentCancelledMailable($appointment));
                // }

            } else {
                $this->setNotification('Estado no válido (' . $newStatus . ') para la cita ID ' . $appointmentId . '.', 'error');
                Log::warning("Intento de actualizar cita ID {$appointmentId} a estado no permitido: {$newStatus}");
            }
        } else {
            $this->setNotification('Cita ID ' . $appointmentId . ' no encontrada.', 'error');
        }
        // No es estrictamente necesario cerrar modales aquí si la acción es inline,
        // pero puede ser útil si se llama desde un modal.
        // $this->closeModals();
    }

    // Establece un mensaje de notificación para mostrar al usuario.
    private function setNotification(string $message, string $type)
    {
        $this->notificationMessage = $message;
        $this->notificationType = $type;
    }

    // Limpia el mensaje de notificación actual.
    private function resetNotification()
    {
        $this->notificationMessage = '';
        $this->notificationType = '';
    }

    // Renderiza la vista del componente Livewire.
    // Se encarga de obtener y pasar los datos necesarios a la plantilla Blade.
    public function render()
    {
        // Configura Carbon para usar el idioma español si no está configurado globalmente.
        // Esto afecta cómo se muestran las fechas (ej. nombres de meses, días).
        // Carbon::setLocale(config('app.locale')); // Ejemplo: 'es'

        // Inicia la consulta para obtener citas, ordenadas por fecha de forma descendente.
        $query = Superappointment::orderBy('appointment_datetime', 'desc');

        // Si hay un término de búsqueda, filtra las citas.
        if (!empty($this->searchTerm)) {
            $searchTermSanitized = '%' . trim($this->searchTerm) . '%'; // Prepara el término para la búsqueda LIKE.
            $query->where(function($q) use ($searchTermSanitized) {
                // Busca en el nombre del invitado, email o nombre del servicio principal.
                $q->where('guest_name', 'like', $searchTermSanitized)
                  ->orWhere('email', 'like', $searchTermSanitized)
                  ->orWhere('primary_service_name', 'like', $searchTermSanitized);
            });
        }

        // Si hay un filtro de estado, aplica el filtro.
        if(!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        // Pagina los resultados, mostrando 10 citas por página.
        $appointments_paginated = $query->paginate(10);

        // Retorna la vista Blade 'livewire.admin.appointment-manager' y le pasa las citas paginadas.
        return view('livewire.admin.appointment-manager', [
            'appointments_paginated' => $appointments_paginated,
        ]);
    }
}
