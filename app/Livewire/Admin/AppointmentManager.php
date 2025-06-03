<?php

namespace App\Livewire\Admin;

use App\Models\Superappointment; // Asegúrate de que el namespace de tu modelo es correcto
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class AppointmentManager extends Component // O AdminAppointmentManager si así se llama tu clase/archivo
{
    use WithPagination;

    public $selectedAppointment;
    public $showModal = false;
    public $showEmailModal = false;

    // Propiedades para el correo electrónico
    public $emailTo;
    public $emailSubject;
    public $emailBody;

    public $searchTerm = '';
    public $statusFilter = ''; // para filtrar por estado: pending, confirmed, cancelled, completed

    public $notificationMessage = '';
    public $notificationType = ''; // 'success', 'error'

    // Reglas de validación para el formulario de correo electrónico
    protected function rules()
    {
        return [
            'emailTo' => 'required|email',
            'emailSubject' => 'required|string|min:3|max:255',
            'emailBody' => 'required|string|min:10|max:5000',
        ];
    }

    // Mensajes de error personalizados para la validación del correo
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

    // Se ejecuta cuando las propiedades con `wire:model.live` o `wire:model.live.debounce` cambian
    public function updatingSearchTerm()
    {
        $this->resetPage(); // Resetea la paginación al buscar
    }

    public function updatingStatusFilter()
    {
        $this->resetPage(); // Resetea la paginación al filtrar
    }

    // Abre el modal con los detalles de la cita
    public function openAppointmentModal(int $appointmentId)
    {
        $this->selectedAppointment = Superappointment::find($appointmentId);
        if ($this->selectedAppointment) {
            $this->showModal = true;
            $this->showEmailModal = false;
            $this->resetNotification();
        }
    }

    // Cierra cualquier modal que esté abierto
    public function closeModals()
    {
        $this->showModal = false;
        $this->showEmailModal = false;
        $this->selectedAppointment = null;
        $this->reset(['emailTo', 'emailSubject', 'emailBody']);
        $this->resetErrorBag(); // Limpiar errores de validación del formulario de email
    }

    // Abre el modal para enviar un correo, precargando datos si se pasa un ID de cita
    public function openEmailModal(int $appointmentId = null)
    {
        $this->resetErrorBag(); // Limpiar errores de validación previos
        if ($appointmentId) {
            $appointment = Superappointment::find($appointmentId);
            if ($appointment) {
                $this->selectedAppointment = $appointment;
                $this->emailTo = $appointment->email;
                $this->emailSubject = "Respuesta a tu cita para " . $appointment->primary_service_name;
                // Formatear fecha y hora usando Carbon
                $appointmentDateTime = Carbon::parse($appointment->appointment_datetime);
                $this->emailBody = "Hola " . $appointment->guest_name . ",\n\n" .
                                   "Con respecto a tu cita programada para el " .
                                   $appointmentDateTime->translatedFormat('l, d \d\e F \d\e Y \a \l\a\s H:i') . // Formato localizado
                                   " (" . $appointmentDateTime->diffForHumans() . ") para el servicio '" . // Tiempo relativo
                                   $appointment->primary_service_name . "':\n\n";
            }
        } else {
            $this->selectedAppointment = null; // Para un correo general (no implementado en la UI actual)
            $this->reset(['emailTo', 'emailSubject', 'emailBody']);
        }
        $this->showModal = false;
        $this->showEmailModal = true;
        $this->resetNotification();
    }

    // Envía el correo personalizado escrito por el admin
    public function sendCustomEmail()
    {
        $this->validate(); // Valida usando las $rules y $messages definidas
        $this->resetNotification();

        try {
            Mail::raw($this->emailBody, function ($message) {
                $message->to($this->emailTo)
                        ->subject($this->emailSubject)
                        ->from(env('MAIL_FROM_ADDRESS', config('mail.from.address')), env('MAIL_FROM_NAME', config('mail.from.name')));
            });

            $this->setNotification('Correo enviado con éxito a ' . $this->emailTo . '.', 'success');
            $this->closeModals();

        } catch (\Exception $e) {
            Log::error('Error al enviar correo personalizado por admin: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->setNotification('Error al enviar el correo. Por favor, inténtalo más tarde o revisa la configuración.', 'error');
        }
    }

    // Actualiza el estado de una cita
    public function updateAppointmentStatus(int $appointmentId, string $newStatus)
    {
        $appointment = Superappointment::find($appointmentId);
        if ($appointment) {
            $allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
            if (in_array($newStatus, $allowedStatuses)) {
                $appointment->status = $newStatus;
                $appointment->save();
                $this->setNotification('Estado de la cita ID ' . $appointment->id . ' actualizado a: ' . ucfirst($newStatus) . '.', 'success');

                // Lógica opcional para notificar al cliente por correo sobre el cambio de estado
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
        // pero no hace daño y cubre el caso si se llama desde el modal.
        // $this->closeModals();
    }

    // Establece un mensaje de notificación
    private function setNotification(string $message, string $type)
    {
        $this->notificationMessage = $message;
        $this->notificationType = $type;
    }

    // Limpia el mensaje de notificación
    private function resetNotification()
    {
        $this->notificationMessage = '';
        $this->notificationType = '';
    }

    // Renderiza la vista del componente
    public function render()
    {
        // Configurar Carbon para español si no está globalmente
        // Carbon::setLocale(config('app.locale')); // Ejemplo: 'es'

        $query = Superappointment::orderBy('appointment_datetime', 'desc');

        if (!empty($this->searchTerm)) {
            $searchTermSanitized = '%' . trim($this->searchTerm) . '%';
            $query->where(function($q) use ($searchTermSanitized) {
                $q->where('guest_name', 'like', $searchTermSanitized)
                  ->orWhere('email', 'like', $searchTermSanitized)
                  ->orWhere('primary_service_name', 'like', $searchTermSanitized);
            });
        }

        if(!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        $appointments_paginated = $query->paginate(10); // Obtener 10 citas por página

        return view('livewire.admin.appointment-manager', [
            'appointments_paginated' => $appointments_paginated,
        ]);
    }
}
