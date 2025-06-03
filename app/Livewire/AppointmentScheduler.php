<?php

namespace App\Livewire;

use App\Models\Superappointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuperAppointmentConfirmationToUser;
use App\Mail\NewSuperAppointmentNotificationToAdmin;
use App\Models\Appointment; // Aunque superappointment es el principal, puede haber lógica relacionada
use App\Models\BusinessSetting; // IMPORTANTE: Modelo para configuraciones de horario
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;

class AppointmentScheduler extends Component
{
    public $pageContext = '';

    public $guest_name = '';
    public $email = '';
    public $phone = '';
    public $primary_service_name = '';
    public $selected_additional_services = [];

    public $appointment_date = '';
    public $appointment_time = '';
    public $notes = '';

    public array $availablePrimaryServices = [];
    public array $availableAdditionalServicesList = [];
    public array $availableTimes = [];
    public $minDate;
    public $maxDate;

    public $successMessage = '';
    public $errorMessage = '';

    // Propiedades para la configuración de horarios (cargadas desde BusinessSetting)
    // Estas almacenarán la configuración activa que usa el calculador de horarios.
    public $activeOpeningHour;
    public $activeClosingHour;
    public $activeLunchBreakStartHour;
    public $activeLunchBreakEndHour;
    public array $activeDisabledDates = [];
    public array $activeDailyHours = []; // Formato: [dayOfWeek => ['open'=>H, 'close'=>H, 'lunch_start'=>H, 'lunch_end'=>H, 'is_closed'=>bool]]

    // Propiedades para que el admin edite la configuración
    public $isUserAdmin = false;
    public $showAdminSettings = false;
    public $editingOpeningHour;
    public $editingClosingHour;
    public $editingLunchBreakStartHour;
    public $editingLunchBreakEndHour;
    public $editingDisabledDatesString = ''; // Para el input de texto
    // Para daily_hours, la edición directa en este formulario sería compleja;
    // se podría manejar en un panel de admin más dedicado.
    // Por ahora, nos centraremos en los globales y fechas desactivadas.


    protected function getServicesDefinition(): array
    {
        return [
            'fotocarnet_cita' => [
                'id' => 'fotocarnet_cita',
                'name' => 'Fotocarnet Cita',
                'description' => 'Sesión rápida para fotos de carnet, DNI, pasaporte.',
                'duration' => 15,
                'price' => 10.00,
                'is_primary' => true,
                'is_additional' => false
            ],
            'llamada_informativa' => [
                'id' => 'llamada_informativa',
                'name' => 'Llamada Informativa Eventos',
                'description' => 'Llamada para discutir detalles de bodas, bautizos, comuniones.',
                'duration' => 30,
                'price' => 0.00,
                'is_primary' => true,
                'is_additional' => true
            ],
            'videoconferencia_eventos' => [
                'id' => 'videoconferencia_eventos',
                'name' => 'Videoconferencia Planificación Eventos',
                'description' => 'Reunión virtual para planificación detallada de eventos.',
                'duration' => 60,
                'price' => 0.00,
                'is_primary' => true,
                'is_additional' => true
            ],
            'retoque_digital_basico' => [
                'id' => 'retoque_digital_basico',
                'name' => 'Retoque Digital Básico',
                'description' => 'Ajustes básicos de luz y color para una foto.',
                'duration' => 0,
                'price' => 5.00,
                'is_primary' => false,
                'is_additional' => true
            ],
            'copias_adicionales_carnet' => [
                'id' => 'copias_adicionales_carnet',
                'name' => 'Juego Adicional Fotos Carnet',
                'description' => 'Un juego extra de fotos de carnet impresas.',
                'duration' => 0,
                'price' => 5.00,
                'is_primary' => false,
                'is_additional' => true
            ],
        ];
    }

    protected function rules(): array
    {
        $primaryServiceKeys = collect($this->getServicesDefinition())->where('is_primary', true)->pluck('id')->toArray();
        $additionalServiceKeys = collect($this->getServicesDefinition())->where('is_additional', true)->pluck('id')->toArray();

        return [
            'guest_name' => Auth::check() ? 'nullable|string|max:255' : 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|regex:/^[+\/\s\-\(\)0-9]{6,20}$/',
            'primary_service_name' => ['required', Rule::in($primaryServiceKeys)],
            'selected_additional_services' => 'array',
            'selected_additional_services.*' => ['string', Rule::in($additionalServiceKeys)],
            'appointment_date' => [
                'required',
                'date',
                'after_or_equal:' . Carbon::today()->toDateString(),
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value);
                    // La validación de fin de semana se hará ahora dentro de getComputedAvailableTimesProperty
                    // en base a la configuración del admin. Si el día está configurado como cerrado, no habrá horas.
                },
            ],
            'appointment_time' => [
                'required',
                 Rule::in(array_keys($this->computedAvailableTimes)) // Asegura que el tiempo esté en la lista generada
            ],
            'notes' => 'nullable|string|max:1000',
        ];
    }

    protected $messages = [
        'guest_name.required' => 'El nombre es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'primary_service_name.required' => 'Debes seleccionar un servicio principal.',
        'primary_service_name.in' => 'El servicio principal seleccionado no es válido.',
        'selected_additional_services.*.in' => 'Uno de los servicios adicionales no es válido.',
        'appointment_time.required' => 'La hora es obligatoria.',
        'appointment_time.in' => 'La hora seleccionada no está disponible.',
    ];

    public function mount($pageContext = null)
    {
        $this->pageContext = $pageContext;
        $this->loadBusinessSettings(); // Cargar configuración de horarios

        $allServices = $this->getServicesDefinition();
        $this->availablePrimaryServices = collect($allServices)->where('is_primary', true)->all();
        $this->availableAdditionalServicesList = collect($allServices)->where('is_additional', true)->all();

        if (Auth::check()) {
            $this->guest_name = Auth::user()->name;
            $this->email = Auth::user()->email;
            // Asumiendo que tu modelo User tiene un accesor isAdmin() o un atributo is_admin
    $this->isUserAdmin = (Auth::user()->role === 'admin');
            if ($this->isUserAdmin) {
                $this->initializeEditingFields();
            }
        }

        $this->minDate = Carbon::today()->toDateString();
        $this->maxDate = Carbon::today()->addMonths(2)->endOfMonth()->toDateString();

        if ($this->pageContext === 'fotocarnet') {
            if (isset($this->availablePrimaryServices['fotocarnet_cita'])) {
                $this->primary_service_name = 'fotocarnet_cita';
            }
        }
        // Es importante que availableTimes se calcule después de cargar la configuración y primary_service_name
        // $this->availableTimes = $this->computedAvailableTimes; // Se llamará desde render o cuando cambien las dependencias
    }

   

protected function loadBusinessSettings()
{
    $settings = BusinessSetting::first();
    if ($settings) {
        $this->activeOpeningHour = $settings->opening_hour;
        $this->activeClosingHour = $settings->closing_hour;
        $this->activeLunchBreakStartHour = $settings->lunch_start_hour;
        $this->activeLunchBreakEndHour = $settings->lunch_end_hour;

        // Solución para activeDisabledDates
        $disabledDatesValue = $settings->disabled_dates;
        $this->activeDisabledDates = is_array($disabledDatesValue) ? $disabledDatesValue : [];

        // Solución para activeDailyHours
        $dailyHoursValue = $settings->daily_hours;
        $this->activeDailyHours = is_array($dailyHoursValue) ? $dailyHoursValue : [];

    } else {
        // Fallback a valores por defecto
        $this->activeOpeningHour = 9;
        $this->activeClosingHour = 19;
        $this->activeLunchBreakStartHour = 14;
        $this->activeLunchBreakEndHour = 15;
        $this->activeDisabledDates = [];
        $this->activeDailyHours = [];
    }
}

    protected function initializeEditingFields()
    {
        $this->editingOpeningHour = $this->activeOpeningHour;
        $this->editingClosingHour = $this->activeClosingHour;
        $this->editingLunchBreakStartHour = $this->activeLunchBreakStartHour;
        $this->editingLunchBreakEndHour = $this->activeLunchBreakEndHour;
        $this->editingDisabledDatesString = implode(', ', $this->activeDisabledDates);
        // La edición de activeDailyHours es más compleja y se omite aquí para simplificar.
    }

    public function getComputedAvailableTimesProperty(): array
    {
        if (empty($this->appointment_date) || empty($this->primary_service_name) || !isset($this->availablePrimaryServices[$this->primary_service_name])) {
            return [];
        }

        $selectedDateCarb = Carbon::parse($this->appointment_date);
        $dateString = $selectedDateCarb->toDateString();

        // 1. Verificar si la fecha está en la lista de fechas deshabilitadas por el admin
        if (in_array($dateString, $this->activeDisabledDates)) {
            return [];
        }

        $dayOfWeek = $selectedDateCarb->dayOfWeekIso; // 1 (Mon) to 7 (Sun)
                                                     // Carbon::dayOfWeek es 0 (Sun) to 6 (Sat)
                                                     // Usaremos Carbon::dayOfWeek para consistencia si así lo prefiere la BD.
                                                     // Para el ejemplo, asumimos que $activeDailyHours usa 0-6.
        $dayOfWeekCarbon = $selectedDateCarb->dayOfWeek; // 0 for Sunday, 1 for Monday, ..., 6 for Saturday.

        $specificDayConfig = $this->activeDailyHours[$dayOfWeekCarbon] ?? null;

        if ($specificDayConfig && isset($specificDayConfig['is_closed']) && $specificDayConfig['is_closed']) {
            return []; // Día específicamente cerrado
        }

        // Usar horarios específicos del día si existen, sino los globales
        $openingHour = $specificDayConfig['open'] ?? $this->activeOpeningHour;
        $closingHour = $specificDayConfig['close'] ?? $this->activeClosingHour;
        $lunchStartHour = $specificDayConfig['lunch_start'] ?? $this->activeLunchBreakStartHour;
        $lunchEndHour = $specificDayConfig['lunch_end'] ?? $this->activeLunchBreakEndHour;

        // Si no hay horas de apertura/cierre (porque no hay globales ni específicas), tratar como cerrado.
        if ($openingHour === null || $closingHour === null) {
             Log::info("Día {$dateString} considerado cerrado por falta de horas de apertura/cierre. Specific: " . json_encode($specificDayConfig) . " Global open: {$this->activeOpeningHour}");
            return [];
        }

        // Si no hay configuración específica para este día de la semana Y el día es fin de semana (según Carbon),
        // Y no hemos definido horas para fines de semana en activeDailyHours, entonces está cerrado.
        // Esta lógica permite abrir fines de semana si se configuran explícitamente en activeDailyHours.
        if (!$specificDayConfig && $selectedDateCarb->isWeekend()) {
            return []; // Cerrado por ser fin de semana y no tener configuración específica que lo abra.
        }


        $serviceData = $this->availablePrimaryServices[$this->primary_service_name];
        $slotDuration = $serviceData['duration'] > 0 ? $serviceData['duration'] : 30;
        $timeSlots = [];

        // Asegurarse de que las horas son enteras antes de usarlas con Carbon
        $openingHour = intval($openingHour);
        $closingHour = intval($closingHour);

        $currentTimeSlot = Carbon::parse($dateString)->hour($openingHour)->minute(0)->second(0);
        $dayEndTime = Carbon::parse($dateString)->hour($closingHour)->minute(0)->second(0);

        $lunchStart = null;
        $lunchEnd = null;
        if ($lunchStartHour !== null && $lunchEndHour !== null && $lunchStartHour < $lunchEndHour) {
            $lunchStart = Carbon::parse($dateString)->hour(intval($lunchStartHour))->minute(0)->second(0);
            $lunchEnd = Carbon::parse($dateString)->hour(intval($lunchEndHour))->minute(0)->second(0);
        }

        while ($currentTimeSlot->copy()->addMinutes($slotDuration)->lte($dayEndTime)) {
            $slotEnd = $currentTimeSlot->copy()->addMinutes($slotDuration);
            $isLunchBreak = false;
            if ($lunchStart && $lunchEnd) {
                // El slot está DENTRO del almuerzo si comienza antes de que termine el almuerzo
                // Y termina después de que comience el almuerzo.
                $isLunchBreak = ($currentTimeSlot->lt($lunchEnd) && $slotEnd->gt($lunchStart));
            }

            if (!$isLunchBreak) {
                $isSlotTaken = Superappointment::where('appointment_datetime', $currentTimeSlot->format('Y-m-d H:i:s'))
                                      // Se podría añadir ->where('primary_service_name', $serviceData['name']) si la disponibilidad es por tipo de servicio.
                                      // Por ahora, un slot ocupado es ocupado para cualquier servicio.
                                      ->whereIn('status', ['pending', 'confirmed'])
                                      ->exists();

                // No permitir reservar con menos de X minutos de antelación (e.g., 15 minutos)
                // Y que el slot no comience en el pasado (ya cubierto por minDate, pero bueno para la hora actual)
                if (!$isSlotTaken && $currentTimeSlot->gte(Carbon::now()->addMinutes(15))) {
                    $timeSlots[$currentTimeSlot->format('H:i')] = $currentTimeSlot->format('H:i');
                }
            }
            // Avanzar al siguiente posible inicio de slot. Intervalos de 15 minutos es común.
            $currentTimeSlot->addMinutes(15);
        }
        return $timeSlots;
    }

    public function updatedPrimaryServiceName($value)
    {
        $this->appointment_date = '';
        $this->appointment_time = '';
        // $this->availableTimes = $this->computedAvailableTimes; // Se actualiza por la propiedad computada
        $this->resetErrorBag('appointment_time'); // Limpiar errores de hora si el servicio cambia
    }

    public function updatedAppointmentDate($value)
    {
        $this->appointment_time = '';
        // $this->availableTimes = $this->computedAvailableTimes; // Se actualiza por la propiedad computada
        if (empty($this->computedAvailableTimes) && !empty($value) ) {
            // Comprobamos si el día está marcado como no laborable por el admin
            $selectedDateCarb = Carbon::parse($value);
            $dateString = $selectedDateCarb->toDateString();
            $dayOfWeekCarbon = $selectedDateCarb->dayOfWeek;
            $specificDayConfig = $this->activeDailyHours[$dayOfWeekCarbon] ?? null;

            if (in_array($dateString, $this->activeDisabledDates) ||
                ($specificDayConfig && isset($specificDayConfig['is_closed']) && $specificDayConfig['is_closed']) ||
                (!$specificDayConfig && $selectedDateCarb->isWeekend() && !($this->activeDailyHours[$dayOfWeekCarbon]['open'] ?? false)) // Fin de semana sin horario específico
            ) {
                 $this->addError('appointment_time', 'Este día no está disponible para citas.');
            } elseif (empty($this->primary_service_name)) {
                 // No hacer nada si no hay servicio principal seleccionado
            }
             else {
                 $this->addError('appointment_time', 'No hay horas disponibles para esta fecha o servicio.');
            }
        } else {
            $this->resetErrorBag('appointment_time');
        }
    }

    public function submitBooking()
    {
        // $this->availableTimes = $this->computedAvailableTimes; // Asegurarse de que está actualizado antes de validar
        $this->validate(); // La validación usará computedAvailableTimes para la regla 'in'

        $this->successMessage = '';
        $this->errorMessage = '';

        try {
            $dateTimeString = $this->appointment_date . ' ' . $this->appointment_time;
            $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTimeString);

            $primaryServiceNameString = $this->availablePrimaryServices[$this->primary_service_name]['name'] ?? 'Servicio Desconocido';
            $additionalServicesNames = collect($this->selected_additional_services)
                ->map(fn($serviceKey) => $this->availableAdditionalServicesList[$serviceKey]['name'] ?? null)
                ->filter()
                ->values()
                ->toArray();

            // Doble verificación de disponibilidad (race condition)
            $isSlotTaken = Superappointment::where('appointment_datetime', $appointmentDateTime)
                                    // ->where('primary_service_name', $primaryServiceNameString) // Opcional si la disponibilidad es por servicio
                                    ->whereIn('status', ['pending', 'confirmed'])
                                    ->exists();
            if ($isSlotTaken) {
                $this->addError('appointment_time', 'Lo sentimos, esta hora acaba de ser reservada. Por favor, elige otra.');
                // $this->availableTimes = $this->computedAvailableTimes; // Refrescar por si acaso
                return;
            }

            $appointmentData = [
                'user_id' => Auth::id(),
                'guest_name' => Auth::check() ? (empty($this->guest_name) ? Auth::user()->name : $this->guest_name) : $this->guest_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'primary_service_name' => $primaryServiceNameString,
                'additional_services' => !empty($additionalServicesNames) ? $additionalServicesNames : null,
                'appointment_datetime' => $appointmentDateTime,
                'notes' => $this->notes,
                'status' => 'pending',
            ];

            Log::info('Datos para crear SUPER cita:', $appointmentData);
            $superappointment = Superappointment::create($appointmentData);

            Log::info("Enviando correos para SUPERAPPOINTMENT ID {$superappointment->id}");
            Mail::to($superappointment->email)->send(new SuperAppointmentConfirmationToUser($superappointment));
            Mail::to(env('ADMIN_APPOINTMENT_NOTIFICATION_EMAIL'))->send(new NewSuperAppointmentNotificationToAdmin($superappointment));

            $this->successMessage = '¡Tu cita ha sido solicitada con éxito! Recibirás un email con los detalles.';
            Log::info("Solicitud de SUPER cita: ID {$superappointment->id} por {$superappointment->email} para {$superappointment->primary_service_name} el {$appointmentDateTime->format('d/m/Y H:i')}");

            $this->resetFormFields();

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errorMessage = 'Por favor, corrige los errores en el formulario.';
             Log::warning("Error de validación al reservar SUPER cita (submitBooking): " . json_encode($e->errors()));
        } catch (\Exception $e) {
            Log::error("Error al reservar SUPER cita (submitBooking): {$e->getMessage()} \n{$e->getTraceAsString()}");
            $this->errorMessage = 'Hubo un error inesperado al procesar tu solicitud.';
        }
    }

    private function resetFormFields()
    {
        $fotocarnetServiceIdKey = null;
        if ($this->pageContext === 'fotocarnet' && isset($this->availablePrimaryServices['fotocarnet_cita'])) {
            $fotocarnetServiceIdKey = 'fotocarnet_cita';
        }

        $this->resetErrorBag();
        $this->resetValidation(); // Limpia los errores de validación
        $this->notes = '';
        $this->appointment_time = '';
        $this->appointment_date = ''; // Esto debería forzar el recálculo de availableTimes
        $this->phone = '';
        $this->selected_additional_services = [];

        if (!Auth::check()) {
            $this->guest_name = '';
            $this->email = '';
        } else {
             $this->guest_name = Auth::user()->name; // Restaurar nombre de usuario logueado
             $this->email = Auth::user()->email; // Restaurar email de usuario logueado
        }

        if ($this->pageContext === 'fotocarnet' && $fotocarnetServiceIdKey) {
            $this->primary_service_name = $fotocarnetServiceIdKey;
        } else {
            $this->primary_service_name = '';
        }
        // $this->availableTimes = $this->computedAvailableTimes; // Se recalculará
    }

    // --- Métodos para Administradores ---
    public function toggleAdminSettings()
    {
        if ($this->isUserAdmin) {
            $this->showAdminSettings = !$this->showAdminSettings;
            if ($this->showAdminSettings) {
                $this->initializeEditingFields(); // Cargar datos actuales al abrir
                 $this->resetErrorBag(); // Limpiar errores de validación de admin si los hubiera
            }
        }
    }

    public function saveAdminSettings()
    {
        if (!$this->isUserAdmin) {
            $this->errorMessage = 'No tienes permisos para realizar esta acción.';
            return;
        }

        $validatedData = $this->validate([
            'editingOpeningHour' => 'required|integer|min:0|max:23',
            'editingClosingHour' => 'required|integer|min:0|max:23|gte:editingOpeningHour',
            'editingLunchBreakStartHour' => 'nullable|integer|min:0|max:23',
            'editingLunchBreakEndHour' => 'nullable|integer|min:0|max:23|gte:editingLunchBreakStartHour',
            'editingDisabledDatesString' => 'nullable|string', // Validación más robusta de formato de fechas podría ser necesaria
        ]);

        try {
            $settings = BusinessSetting::firstOrCreate(['id' => 1]); // Obtener o crear la fila de configuración

            $disabledDatesArray = [];
            if (!empty($validatedData['editingDisabledDatesString'])) {
                $rawDates = explode(',', $validatedData['editingDisabledDatesString']);
                foreach ($rawDates as $dateStr) {
                    try {
                        $disabledDatesArray[] = Carbon::parse(trim($dateStr))->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Opcional: añadir error si una fecha no es válida
                        // $this->addError('editingDisabledDatesString', 'Fecha inválida: ' . trim($dateStr));
                        // return;
                        Log::warning("Formato de fecha inválido en admin settings: " . trim($dateStr));
                    }
                }
                $disabledDatesArray = array_unique($disabledDatesArray); // Evitar duplicados
                sort($disabledDatesArray); // Ordenar
            }


            $settings->update([
                'opening_hour' => $validatedData['editingOpeningHour'],
                'closing_hour' => $validatedData['editingClosingHour'],
                'lunch_start_hour' => $validatedData['editingLunchBreakStartHour'],
                'lunch_end_hour' => $validatedData['editingLunchBreakEndHour'],
                'disabled_dates' => $disabledDatesArray,
                // 'daily_hours' => $this->activeDailyHours, // Si se implementara edición de daily_hours
            ]);

            $this->loadBusinessSettings(); // Recargar la configuración activa
            $this->initializeEditingFields(); // Refrescar campos de edición con lo guardado

            $this->successMessage = 'Configuración de horarios guardada correctamente.';
            $this->errorMessage = ''; // Limpiar cualquier error previo
            $this->showAdminSettings = false; // Opcional: ocultar el panel tras guardar

            // Forzar recálculo de horas disponibles si una fecha estaba seleccionada
            if ($this->appointment_date) {
                $this->updatedAppointmentDate($this->appointment_date);
            }

        } catch (\Exception $e) {
            Log::error("Error al guardar configuración de admin: {$e->getMessage()}");
            $this->errorMessage = 'Error al guardar la configuración: ' . $e->getMessage();
        }
    }


    public function render()
    {
        // La propiedad computada $this->computedAvailableTimes se actualiza automáticamente cuando
        // $this->appointment_date o $this->primary_service_name cambian.
        // Si queremos que $this->availableTimes sea la fuente de verdad para el select de horas,
        // debemos asegurarnos de que se actualiza.
        // Ya que `computedAvailableTimes` es una propiedad computada, `availableTimes` podría simplemente ser una alias o
        // actualizarse explícitamente en los `updated` hooks.
        // Para simplificar, la vista usará directamente `computedAvailableTimes` o nos aseguraremos de que
        // `availableTimes` se actualice.

        // Aquí, nos aseguramos que `availableTimes` tenga el valor más reciente antes de renderizar.
        // Aunque las propiedades computadas deberían hacer esto reactivo, a veces con `wire:model.defer`
        // es bueno reconfirmar.
        // if ($this->appointment_date && $this->primary_service_name) {
             // $this->availableTimes = $this->computedAvailableTimes;
        // } else {
             // $this->availableTimes = [];
        // }
        // No es necesario lo anterior si la vista usa $this->computedAvailableTimes directamente
        // o si los `updated` hooks ya lo hacen.
        // Dado que las reglas de validación usan $this->computedAvailableTimes, está bien.

        return view('livewire.appointment-scheduler', [
            // Pasar explícitamente las horas disponibles a la vista si es necesario,
            // aunque la vista puede acceder a las propiedades públicas directamente.
            // 'currentAvailableTimes' => $this->computedAvailableTimes
        ]);
    }
}
