<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFabNotification;
use Illuminate\Support\Facades\Log;
use App\Models\Superappointment; // Usando el modelo correcto
use Carbon\Carbon;

class ContactFab extends Component
{
    public bool $showModal = false;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $description = '';
    public bool $formSubmitted = false;

    private string $fixedPurpose = "Solicitar información";

    protected function rules(): array
    {
        return [
            'name'        => 'required|string|min:3|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|string|regex:/^[+\d\s\(\)-]*$/|min:9|max:20',
            'description' => 'required|string|min:10|max:2000',
        ];
    }

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

    public function toggleModal(): void
    {
        $this->showModal = !$this->showModal;
        if (!$this->showModal) {
            $this->resetFormAndValidation();
        } else {
            $this->formSubmitted = false;
            $this->resetValidation();
        }
    }

    public function submitForm(): void
    {
        $validatedData = $this->validate();
        $currentDateTime = Carbon::now();

        try {
            $superappointment = new Superappointment();
            $superappointment->guest_name = $validatedData['name'];
            $superappointment->email = $validatedData['email']; // <<--- CORREGIDO a 'email'
            $superappointment->phone = $validatedData['phone'] ?? null; // <<--- CORREGIDO a 'phone'

            $superappointment->primary_service_name = $this->fixedPurpose;
            $superappointment->notes = "Descripción del usuario:\n" . $validatedData['description'];
            $superappointment->appointment_datetime = $currentDateTime;
            $superappointment->status = 'FAB_INFO_REQUEST';
            $superappointment->status = 'pending'; // Usar un valor válido del ENUM

            // Asegúrate que otros campos NOT NULL en tu tabla 'superappointments'
            // tengan un valor por defecto en la BD, o asígnalos aquí si es necesario.
            // Por ejemplo, si 'user_id' no es nullable y no hay valor por defecto:
            // $superappointment->user_id = null; // O algún valor si aplica y la BD lo permite
            // $superappointment->additional_services = []; // Si es JSON y no nullable

            $superappointment->save();

            $adminEmail = env('ADMIN_CONTACT_EMAIL');
            if ($adminEmail) {
                Mail::to($adminEmail)
                    ->send(new ContactFabNotification(
                        $validatedData['name'],
                        $validatedData['email'],
                        $validatedData['phone'] ?? '',
                        $this->fixedPurpose,
                        $validatedData['description'],
                        $currentDateTime
                    ));
                Log::info('Solicitud de información (FAB) enviada a admin y guardada en Superappointments.', ['data' => $validatedData, 'id' => $superappointment->id]);
            } else {
                Log::warning('ADMIN_CONTACT_EMAIL no configurado. Solicitud de FAB guardada en BD pero no notificada por email.', ['data' => $validatedData, 'id' => $superappointment->id]);
            }

            $this->formSubmitted = true;

        } catch (\Illuminate\Database\QueryException $e) { // Captura QueryException específicamente
            Log::error('Error de Base de Datos al procesar solicitud (FAB): ' . $e->getMessage(), [
                'exception' => $e,
                'sql' => $e->getSql(), // Obtener el SQL que falló
                'bindings' => $e->getBindings(), // Obtener los bindings del SQL
                'data' => $validatedData
            ]);
            session()->flash('fab_error', 'Hubo un problema técnico al guardar tu solicitud (DB). Por favor, intenta más tarde.');
            $this->formSubmitted = false;
        }
         catch (\Exception $e) { // Captura otras excepciones
            Log::error('Error general al procesar solicitud (FAB): ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $validatedData
            ]);
            session()->flash('fab_error', 'No se pudo procesar tu solicitud en este momento. Por favor, inténtalo de nuevo más tarde.');
            $this->formSubmitted = false;
        }
    }

    public function resetFormAndValidation(): void
    {
        $this->reset(['name', 'email', 'phone', 'description', 'formSubmitted']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.contact-fab', ['fixedPurpose' => $this->fixedPurpose]);
    }
}
