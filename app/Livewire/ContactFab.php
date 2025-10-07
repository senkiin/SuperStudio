<?php

namespace App\Livewire;

use Livewire\Component; // Clase base para todos los componentes Livewire.
use Illuminate\Support\Facades\Mail; // Fachada para enviar correos electrónicos.
use App\Mail\ContactFabNotification; // Mailable para notificar al administrador sobre un nuevo contacto.
use Illuminate\Support\Facades\Log;  // Fachada para registrar logs, útil para depuración.
use App\Models\Superappointment; // Modelo para guardar la solicitud de contacto como una "cita" especial.
use Carbon\Carbon; // Librería para manejar fechas y horas de forma sencilla.

class ContactFab extends Component
{
    // --- Propiedades del Componente ---
    public bool $showModal = false; // Controla la visibilidad del modal de contacto.
    public string $name = '';       // Nombre del contacto.
    public string $email = '';      // Email del contacto.
    public string $phone = '';      // Teléfono del contacto.
    public string $description = ''; // Descripción o mensaje del contacto.
    public bool $formSubmitted = false; // Indica si el formulario se ha enviado con éxito.

    // Propósito fijo para este formulario de contacto.
    // Podría ser dinámico si el FAB tuviera múltiples propósitos.
    private string $fixedPurpose = "Solicitar información";

    /**
     * Define las reglas de validación para el formulario de contacto.
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name'        => 'required|string|min:3|max:255', // Nombre: obligatorio, texto, mín 3, máx 255 caracteres.
            'email'       => 'required|email|max:255',       // Email: obligatorio, formato email válido, máx 255 caracteres.
            // Teléfono: opcional, texto, formato flexible (números, espacios, paréntesis, guiones), mín 9, máx 20 caracteres.
            'phone'       => 'nullable|string|regex:/^[+\d\s\(\)-]*$/|min:9|max:20',
            'description' => 'required|string|min:10|max:2000', // Descripción: obligatoria, texto, mín 10, máx 2000 caracteres.
        ];
    }

    /**
     * Define mensajes de error personalizados para las reglas de validación.
     * @var array
     */
    protected array $messages = [
        'name.required'          => 'El nombre es obligatorio.',
        'email.required'         => 'El correo electrónico es obligatorio.',
        'email.email'            => 'El formato del correo no es válido.',
        'phone.regex'            => 'El formato del teléfono no es válido.',
        'phone.min'              => 'El teléfono debe tener al menos 9 caracteres.',
        'phone.max'              => 'El teléfono no debe exceder los 20 caracteres.',
        'description.required'   => 'El mensaje es obligatorio.',
        'description.min'        => 'El mensaje debe tener al menos 10 caracteres.',
    ];

    /**
     * Define los eventos que este componente escucha.
     * @var array
     */
    protected $listeners = ['openContactModal'];

    /**
     * Abre el modal de contacto cuando se dispara el evento.
     */
    public function openContactModal(): void
    {
        $this->showModal = true;
        $this->formSubmitted = false;
        $this->resetValidation();
    }

    /**
     * Muestra u oculta el modal de contacto.
     * Si se oculta, resetea el formulario y la validación.
     * Si se muestra, resetea el estado de envío y la validación.
     */
    public function toggleModal(): void
    {
        $this->showModal = !$this->showModal;
        if (!$this->showModal) { // Si se está cerrando el modal.
            $this->resetFormAndValidation();
        } else { // Si se está abriendo el modal.
            $this->formSubmitted = false; // Asegura que no se muestre el mensaje de éxito de un envío anterior.
            $this->resetValidation(); // Limpia errores de validación previos.
        }
    }

    /**
     * Procesa el envío del formulario de contacto.
     * Valida los datos, guarda la solicitud como una Superappointment,
     * y envía una notificación por email al administrador.
     */
    public function submitForm(): void
    {
        $validatedData = $this->validate(); // Valida los campos del formulario.
        $currentDateTime = Carbon::now(); // Obtiene la fecha y hora actual.

        try {
            // Crea una nueva instancia de Superappointment para registrar la solicitud.
            $superappointment = new Superappointment();
            $superappointment->guest_name = $validatedData['name'];
            $superappointment->email = $validatedData['email']; // Asigna el email validado.
            $superappointment->phone = $validatedData['phone'] ?? null; // Asigna el teléfono validado, o null si está vacío.

            $superappointment->primary_service_name = $this->fixedPurpose; // Asigna el propósito fijo.
            $superappointment->notes = "Descripción del usuario:\n" . $validatedData['description']; // Combina con la descripción.
            $superappointment->appointment_datetime = $currentDateTime; // Usa la fecha y hora actual como referencia.
            // $superappointment->status = 'FAB_INFO_REQUEST'; // Estado personalizado (comentado).
            $superappointment->status = 'pending'; // Usa un estado válido del ENUM definido en la migración.

            // Asegúrate de que otros campos NOT NULL en tu tabla 'superappointments'
            // tengan un valor por defecto en la BD, o asígnalos aquí si es necesario.
            // Por ejemplo, si 'user_id' no es nullable y no hay valor por defecto:
            // $superappointment->user_id = null; // O algún valor si aplica y la BD lo permite
            // $superappointment->additional_services = []; // Si es JSON y no nullable, un array vacío es un buen default.

            $superappointment->save(); // Guarda la solicitud en la base de datos.

            $adminEmail = env('ADMIN_CONTACT_EMAIL'); // Obtiene el email del administrador desde el .env.
            if ($adminEmail) {
                // Envía la notificación por email al administrador.
                Mail::to($adminEmail)
                    ->send(new ContactFabNotification(
                        $validatedData['name'],
                        $validatedData['email'],
                        $validatedData['phone'] ?? '', // Teléfono, o string vacío si es null.
                        $this->fixedPurpose,
                        $validatedData['description'],
                        $currentDateTime
                    ));
                Log::info('Solicitud de información (FAB) enviada a admin y guardada en Superappointments.', ['data' => $validatedData, 'id' => $superappointment->id]);
            } else {
                Log::warning('ADMIN_CONTACT_EMAIL no configurado. Solicitud de FAB guardada en BD pero no notificada por email.', ['data' => $validatedData, 'id' => $superappointment->id]);
            }

            $this->formSubmitted = true; // Marca el formulario como enviado con éxito para mostrar mensaje al usuario.

        } catch (\Illuminate\Database\QueryException $e) { // Captura excepciones específicas de la base de datos.
            Log::error('Error de Base de Datos al procesar solicitud (FAB): ' . $e->getMessage(), [
                'exception' => $e,
                'sql' => $e->getSql(), // Registra la consulta SQL que falló.
                'bindings' => $e->getBindings(), // Registra los parámetros de la consulta.
                'data' => $validatedData
            ]);
            session()->flash('fab_error', 'Hubo un problema técnico al guardar tu solicitud (DB). Por favor, intenta más tarde.');
            $this->formSubmitted = false; // Asegura que no se muestre el mensaje de éxito si falló.
        }
         catch (\Exception $e) { // Captura cualquier otra excepción general.
            Log::error('Error general al procesar solicitud (FAB): ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $validatedData
            ]);
            session()->flash('fab_error', 'No se pudo procesar tu solicitud en este momento. Por favor, inténtalo de nuevo más tarde.');
            $this->formSubmitted = false;
        }
    }

    /**
     * Resetea los campos del formulario y el estado de validación.
     */
    public function resetFormAndValidation(): void
    {
        // Resetea las propiedades del formulario a sus valores iniciales.
        $this->reset(['name', 'email', 'phone', 'description', 'formSubmitted']);
        $this->resetValidation(); // Limpia los errores de validación de Livewire.
    }

    /**
     * Renderiza la vista del componente.
     * Pasa el propósito fijo a la vista Blade.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // Retorna la vista Blade 'livewire.contact-fab' y le pasa el propósito fijo.
        return view('livewire.contact-fab', ['fixedPurpose' => $this->fixedPurpose]);
    }
}
