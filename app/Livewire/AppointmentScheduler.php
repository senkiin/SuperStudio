<?php

namespace App\Livewire;

use App\Models\Superappointment; // Modelo para guardar las citas (asumo que es el modelo principal para citas).
use Carbon\Carbon; // Librería para manejar fechas y horas de forma sencilla.
use Illuminate\Support\Facades\Mail; // Fachada para enviar correos electrónicos.
use App\Mail\SuperAppointmentConfirmationToUser; // Mailable para confirmar la cita al usuario.
use App\Mail\NewSuperAppointmentNotificationToAdmin; // Mailable para notificar al admin de una nueva cita.
use App\Models\Appointment; // Modelo de citas (mencionado, aunque Superappointment parece ser el principal).
use App\Models\BusinessSetting; // IMPORTANTE: Modelo para obtener la configuración de horarios del negocio.
use Illuminate\Support\Facades\Auth; // Fachada para acceder a la información del usuario autenticado.
use Livewire\Component; // Clase base para todos los componentes Livewire.
use Illuminate\Support\Facades\Log; // Fachada para registrar logs, útil para depuración.
use Illuminate\Validation\Rule; // Clase Rule para validaciones más complejas (ej. 'in').
use Illuminate\Support\Collection; // Clase Collection de Laravel para un manejo de arrays más fluido.

class AppointmentScheduler extends Component
{
    // --- Propiedades del Componente ---
    public $pageContext = ''; // Contexto de la página (ej. 'fotocarnet') para preseleccionar servicios.

    // --- Propiedades del Formulario de Reserva ---
    public $guest_name = ''; // Nombre del invitado (si no está logueado).
    public $email = '';      // Email del cliente.
    public $phone = '';      // Teléfono del cliente.
    public $primary_service_name = ''; // Clave del servicio principal seleccionado (ej. 'fotocarnet_cita').
    public $selected_additional_services = []; // Array de claves de servicios adicionales seleccionados.

    public $appointment_date = ''; // Fecha seleccionada para la cita (YYYY-MM-DD).
    public $appointment_time = ''; // Hora seleccionada para la cita (HH:MM).
    public $notes = '';            // Notas adicionales para la cita.

    // --- Datos para Selectores y Lógica de Disponibilidad ---
    public array $availablePrimaryServices = []; // Array de servicios principales disponibles.
    public array $availableAdditionalServicesList = []; // Array de todos los servicios adicionales disponibles.
    public array $availableTimes = []; // Horas disponibles para la fecha y servicio seleccionados (se calcula).
    public $minDate; // Fecha mínima seleccionable (hoy).
    public $maxDate; // Fecha máxima seleccionable (ej. 2 meses en el futuro).

    // --- Mensajes para el Usuario ---
    public $successMessage = ''; // Mensaje de éxito tras la reserva.
    public $errorMessage = '';   // Mensaje de error si algo falla.

    // --- Propiedades para la Configuración de Horarios (cargadas desde BusinessSetting) ---
    // Estas almacenarán la configuración activa que usa el calculador de horarios.
    public $activeOpeningHour;        // Hora de apertura general.
    public $activeClosingHour;        // Hora de cierre general.
    public $activeLunchBreakStartHour; // Inicio de la pausa para comer.
    public $activeLunchBreakEndHour;  // Fin de la pausa para comer.
    public array $activeDisabledDates = []; // Array de fechas específicas deshabilitadas (ej. '2023-12-25').
    public array $activeDailyHours = [];    // Configuración de horarios por día de la semana.
                                          // Formato esperado: [dayOfWeekCarbon => ['open'=>H, 'close'=>H, 'lunch_start'=>H, 'lunch_end'=>H, 'is_closed'=>bool]]
                                          // donde dayOfWeekCarbon es 0 (Domingo) a 6 (Sábado).

    // --- Propiedades para que el Admin Edite la Configuración ---
    public $isUserAdmin = false; // Indica si el usuario actual es administrador.
    public $showAdminSettings = false; // Controla la visibilidad del panel de configuración para admin.
    public $editingOpeningHour;
    public $editingClosingHour;
    public $editingLunchBreakStartHour;
    public $editingLunchBreakEndHour;
    public $editingDisabledDatesString = ''; // String de fechas deshabilitadas separadas por comas para el input del admin.
    // La edición de activeDailyHours directamente en este formulario sería compleja;
    // se podría manejar en un panel de admin más dedicado.
    // Por ahora, nos centraremos en los globales y fechas desactivadas.


    /**
     * Define la lista de servicios disponibles con sus propiedades.
     * Esta información podría venir de la base de datos en una aplicación más compleja.
     * @return array
     */
    protected function getServicesDefinition(): array
    {
        return [
            'fotocarnet_cita' => [
                'id' => 'fotocarnet_cita',
                'name' => 'Fotocarnet Cita',
                'description' => 'Sesión rápida para fotos de carnet, DNI, pasaporte.',
                'duration' => 15, // Duración en minutos
                'price' => 10.00,
                'is_primary' => true, // Puede ser seleccionado como servicio principal
                'is_additional' => false // No puede ser seleccionado como adicional a otro
            ],
            'llamada_informativa' => [
                'id' => 'llamada_informativa',
                'name' => 'Llamada Informativa Eventos',
                'description' => 'Llamada para discutir detalles de bodas, bautizos, comuniones.',
                'duration' => 30,
                'price' => 0.00,
                'is_primary' => true,
                'is_additional' => true // Puede ser adicional a otro servicio de evento
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
                'duration' => 0, // No añade tiempo a la cita
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

    /**
     * Define las reglas de validación para el formulario de reserva.
     * @return array
     */
    protected function rules(): array
    {
        // Obtiene las claves de los servicios primarios y adicionales para usarlas en las reglas 'in'.
        $primaryServiceKeys = collect($this->getServicesDefinition())->where('is_primary', true)->pluck('id')->toArray();
        $additionalServiceKeys = collect($this->getServicesDefinition())->where('is_additional', true)->pluck('id')->toArray();

        return [
            // El nombre es opcional si el usuario está logueado, requerido si es invitado.
            'guest_name' => Auth::check() ? 'nullable|string|max:255' : 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|regex:/^[+\/\s\-\(\)0-9]{6,20}$/', // Expresión regular para validar formatos de teléfono comunes.
            'primary_service_name' => ['required', Rule::in($primaryServiceKeys)], // Debe ser uno de los servicios primarios definidos.
            'selected_additional_services' => 'array', // Debe ser un array.
            'selected_additional_services.*' => ['string', Rule::in($additionalServiceKeys)], // Cada elemento debe ser un servicio adicional válido.
            'appointment_date' => [
                'required',
                'date',
                'after_or_equal:' . Carbon::today()->toDateString(), // La fecha debe ser hoy o en el futuro.
                // Validación personalizada para días de fin de semana (se maneja ahora en computedAvailableTimes).
                // function ($attribute, $value, $fail) {
                //     $date = Carbon::parse($value);
                // },
            ],
            'appointment_time' => [
                'required',
                 Rule::in(array_keys($this->computedAvailableTimes)) // La hora seleccionada debe estar en la lista de horas disponibles calculadas.
            ],
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Define mensajes de error personalizados para las reglas de validación.
     */
    protected $messages = [
        'guest_name.required' => 'El nombre es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'primary_service_name.required' => 'Debes seleccionar un servicio principal.',
        'primary_service_name.in' => 'El servicio principal seleccionado no es válido.',
        'selected_additional_services.*.in' => 'Uno de los servicios adicionales no es válido.',
        'appointment_time.required' => 'La hora es obligatoria.',
        'appointment_time.in' => 'La hora seleccionada no está disponible.',
    ];

    /**
     * Método `mount`: Se ejecuta una vez cuando el componente se inicializa.
     * @param string|null $pageContext Contexto de la página para preseleccionar servicios.
     */
    public function mount($pageContext = null)
    {
        $this->pageContext = $pageContext;
        $this->loadBusinessSettings(); // Carga la configuración de horarios del negocio.

        // Prepara las listas de servicios primarios y adicionales.
        $allServices = $this->getServicesDefinition();
        $this->availablePrimaryServices = collect($allServices)->where('is_primary', true)->all();
        $this->availableAdditionalServicesList = collect($allServices)->where('is_additional', true)->all();

        // Si el usuario está autenticado, precarga su nombre y email.
        // También verifica si es administrador para mostrar opciones de configuración.
        if (Auth::check()) {
            $this->guest_name = Auth::user()->name;
            $this->email = Auth::user()->email;
            $this->isUserAdmin = (Auth::user()->role === 'admin'); // Asume que el rol 'admin' existe.
            if ($this->isUserAdmin) {
                $this->initializeEditingFields(); // Prepara los campos para editar la configuración.
            }
        }

        // Establece las fechas mínima y máxima para el selector de fechas.
        $this->minDate = Carbon::today()->toDateString();
        $this->maxDate = Carbon::today()->addMonths(2)->endOfMonth()->toDateString();

        // Si el contexto es 'fotocarnet', preselecciona el servicio de fotocarnet.
        if ($this->pageContext === 'fotocarnet') {
            if (isset($this->availablePrimaryServices['fotocarnet_cita'])) {
                $this->primary_service_name = 'fotocarnet_cita';
            }
        }
        // Las horas disponibles ($availableTimes) se calcularán dinámicamente mediante la propiedad computada.
    }


    /**
     * Carga la configuración de horarios desde el modelo BusinessSetting.
     * Si no existe configuración, usa valores por defecto.
     */
    protected function loadBusinessSettings()
    {
        $settings = BusinessSetting::first(); // Asume que solo hay una fila de configuración.
        if ($settings) {
            $this->activeOpeningHour = $settings->opening_hour;
            $this->activeClosingHour = $settings->closing_hour;
            $this->activeLunchBreakStartHour = $settings->lunch_start_hour;
            $this->activeLunchBreakEndHour = $settings->lunch_end_hour;

            // Asegura que 'disabled_dates' y 'daily_hours' sean arrays, incluso si son null en la BD.
            $disabledDatesValue = $settings->disabled_dates;
            $this->activeDisabledDates = is_array($disabledDatesValue) ? $disabledDatesValue : [];

            $dailyHoursValue = $settings->daily_hours;
            $this->activeDailyHours = is_array($dailyHoursValue) ? $dailyHoursValue : [];

        } else {
            // Valores por defecto si no hay configuración guardada.
            $this->activeOpeningHour = 9;
            $this->activeClosingHour = 19;
            $this->activeLunchBreakStartHour = 14;
            $this->activeLunchBreakEndHour = 15;
            $this->activeDisabledDates = [];
            $this->activeDailyHours = [];
        }
    }

    /**
     * Inicializa los campos del formulario de edición de configuración para el administrador
     * con los valores activos actuales.
     */
    protected function initializeEditingFields()
    {
        $this->editingOpeningHour = $this->activeOpeningHour;
        $this->editingClosingHour = $this->activeClosingHour;
        $this->editingLunchBreakStartHour = $this->activeLunchBreakStartHour;
        $this->editingLunchBreakEndHour = $this->activeLunchBreakEndHour;
        // Convierte el array de fechas deshabilitadas a un string para el input.
        $this->editingDisabledDatesString = implode(', ', $this->activeDisabledDates);
    }

    /**
     * Propiedad computada: Calcula las horas disponibles para la cita.
     * Esta es la lógica central para determinar la disponibilidad.
     * Se recalcula automáticamente cuando cambian $appointment_date o $primary_service_name.
     * @return array
     */
    public function getComputedAvailableTimesProperty(): array
    {
        // Si no hay fecha o servicio principal seleccionado, no hay horas disponibles.
        if (empty($this->appointment_date) || empty($this->primary_service_name) || !isset($this->availablePrimaryServices[$this->primary_service_name])) {
            return [];
        }

        $selectedDateCarb = Carbon::parse($this->appointment_date);
        $dateString = $selectedDateCarb->toDateString();

        // 1. Verificar si la fecha está en la lista de fechas deshabilitadas globalmente por el admin.
        if (in_array($dateString, $this->activeDisabledDates)) {
            return []; // Fecha deshabilitada.
        }

        $dayOfWeekCarbon = $selectedDateCarb->dayOfWeek; // 0 (Domingo) a 6 (Sábado).
        // Obtiene la configuración específica para este día de la semana, si existe.
        $specificDayConfig = $this->activeDailyHours[$dayOfWeekCarbon] ?? null;

        // 2. Verificar si el día está específicamente marcado como cerrado en la configuración diaria.
        if ($specificDayConfig && isset($specificDayConfig['is_closed']) && $specificDayConfig['is_closed']) {
            return []; // Día específicamente cerrado.
        }

        // 3. Determinar las horas de apertura/cierre y almuerzo para el día seleccionado.
        // Se usan los horarios específicos del día si existen; si no, se usan los globales.
        $openingHour = $specificDayConfig['open'] ?? $this->activeOpeningHour;
        $closingHour = $specificDayConfig['close'] ?? $this->activeClosingHour;
        $lunchStartHour = $specificDayConfig['lunch_start'] ?? $this->activeLunchBreakStartHour;
        $lunchEndHour = $specificDayConfig['lunch_end'] ?? $this->activeLunchBreakEndHour;

        // Si no hay horas de apertura/cierre definidas (ni global ni específicamente), el día se considera cerrado.
        if ($openingHour === null || $closingHour === null) {
             Log::info("Día {$dateString} considerado cerrado por falta de horas de apertura/cierre. Specific: " . json_encode($specificDayConfig) . " Global open: {$this->activeOpeningHour}");
            return [];
        }

        // 4. Manejar fines de semana: si es fin de semana Y no hay configuración específica que lo abra, está cerrado.
        if (!$specificDayConfig && $selectedDateCarb->isWeekend()) {
            return [];
        }

        // Obtiene la duración del servicio principal seleccionado.
        $serviceData = $this->availablePrimaryServices[$this->primary_service_name];
        $slotDuration = $serviceData['duration'] > 0 ? $serviceData['duration'] : 30; // Duración mínima de 30 min si no se especifica.
        $timeSlots = [];

        // Asegura que las horas sean enteras para usarlas con Carbon.
        $openingHour = intval($openingHour);
        $closingHour = intval($closingHour);

        // Inicializa el primer slot posible del día.
        $currentTimeSlot = Carbon::parse($dateString)->hour($openingHour)->minute(0)->second(0);
        $dayEndTime = Carbon::parse($dateString)->hour($closingHour)->minute(0)->second(0); // Hora de cierre.

        // Prepara los objetos Carbon para la pausa de almuerzo, si está definida.
        $lunchStart = null;
        $lunchEnd = null;
        if ($lunchStartHour !== null && $lunchEndHour !== null && intval($lunchStartHour) < intval($lunchEndHour)) {
            $lunchStart = Carbon::parse($dateString)->hour(intval($lunchStartHour))->minute(0)->second(0);
            $lunchEnd = Carbon::parse($dateString)->hour(intval($lunchEndHour))->minute(0)->second(0);
        }

        // Itera generando slots de tiempo hasta alcanzar la hora de cierre.
        while ($currentTimeSlot->copy()->addMinutes($slotDuration)->lte($dayEndTime)) {
            $slotEnd = $currentTimeSlot->copy()->addMinutes($slotDuration);
            $isLunchBreak = false;
            // Verifica si el slot actual cae dentro de la pausa de almuerzo.
            if ($lunchStart && $lunchEnd) {
                $isLunchBreak = ($currentTimeSlot->lt($lunchEnd) && $slotEnd->gt($lunchStart));
            }

            if (!$isLunchBreak) {
                // Verifica si el slot ya está ocupado por otra cita (pendiente o confirmada).
                $isSlotTaken = Superappointment::where('appointment_datetime', $currentTimeSlot->format('Y-m-d H:i:s'))
                                      ->whereIn('status', ['pending', 'confirmed'])
                                      ->exists();

                // No permite reservar con menos de X minutos de antelación (ej. 15 min) y que no sea en el pasado.
                if (!$isSlotTaken && $currentTimeSlot->gte(Carbon::now()->addMinutes(15))) {
                    $timeSlots[$currentTimeSlot->format('H:i')] = $currentTimeSlot->format('H:i');
                }
            }
            // Avanza al siguiente posible inicio de slot (intervalos de 15 minutos es común para dar flexibilidad).
            $currentTimeSlot->addMinutes(15);
        }
        return $timeSlots;
    }

    /**
     * Hook de Livewire: Se ejecuta cuando la propiedad $primary_service_name se actualiza.
     * Resetea la fecha y hora de la cita y limpia errores relacionados.
     */
    public function updatedPrimaryServiceName($value)
    {
        $this->appointment_date = '';
        $this->appointment_time = '';
        $this->resetErrorBag('appointment_time');
    }

    /**
     * Hook de Livewire: Se ejecuta cuando la propiedad $appointment_date se actualiza.
     * Resetea la hora de la cita. Si no hay horas disponibles para la nueva fecha,
     * muestra un mensaje de error apropiado.
     */
    public function updatedAppointmentDate($value)
    {
        $this->appointment_time = ''; // Resetea la hora seleccionada.
        // La propiedad computada $this->computedAvailableTimes se recalculará automáticamente.
        // Verifica si, tras el recálculo, no hay horas disponibles y la fecha es válida.
        if (empty($this->computedAvailableTimes) && !empty($value) ) {
            $selectedDateCarb = Carbon::parse($value);
            $dateString = $selectedDateCarb->toDateString();
            $dayOfWeekCarbon = $selectedDateCarb->dayOfWeek;
            $specificDayConfig = $this->activeDailyHours[$dayOfWeekCarbon] ?? null;

            // Comprueba las razones por las que el día podría no estar disponible.
            if (in_array($dateString, $this->activeDisabledDates) ||
                ($specificDayConfig && isset($specificDayConfig['is_closed']) && $specificDayConfig['is_closed']) ||
                (!$specificDayConfig && $selectedDateCarb->isWeekend() && !($this->activeDailyHours[$dayOfWeekCarbon]['open'] ?? false))
            ) {
                 $this->addError('appointment_time', 'Este día no está disponible para citas.');
            } elseif (empty($this->primary_service_name)) {
                 // No muestra error si aún no se ha seleccionado un servicio principal.
            }
             else { // Si el día es laborable pero no hay slots (ej. todos ocupados o servicio muy largo).
                 $this->addError('appointment_time', 'No hay horas disponibles para esta fecha o servicio.');
            }
        } else {
            $this->resetErrorBag('appointment_time'); // Limpia errores si hay horas disponibles.
        }
    }

    /**
     * Procesa la solicitud de reserva de cita.
     * Valida los datos, crea la cita, y envía notificaciones por email.
     */
    public function submitBooking()
    {
        $this->validate(); // Valida el formulario.

        $this->successMessage = ''; // Limpia mensajes previos.
        $this->errorMessage = '';

        try {
            // Combina fecha y hora para crear el objeto Carbon de la cita.
            $dateTimeString = $this->appointment_date . ' ' . $this->appointment_time;
            $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTimeString);

            // Obtiene los nombres legibles de los servicios seleccionados.
            $primaryServiceNameString = $this->availablePrimaryServices[$this->primary_service_name]['name'] ?? 'Servicio Desconocido';
            $additionalServicesNames = collect($this->selected_additional_services)
                ->map(fn($serviceKey) => $this->availableAdditionalServicesList[$serviceKey]['name'] ?? null)
                ->filter() // Elimina nulos si alguna clave no es válida.
                ->values()
                ->toArray();

            // Doble verificación de disponibilidad para evitar race conditions.
            $isSlotTaken = Superappointment::where('appointment_datetime', $appointmentDateTime)
                                    ->whereIn('status', ['pending', 'confirmed'])
                                    ->exists();
            if ($isSlotTaken) {
                $this->addError('appointment_time', 'Lo sentimos, esta hora acaba de ser reservada. Por favor, elige otra.');
                return;
            }

            // Prepara los datos para crear la Superappointment.
            $appointmentData = [
                'user_id' => Auth::id(), // ID del usuario logueado, o null si es invitado.
                'guest_name' => Auth::check() ? (empty($this->guest_name) ? Auth::user()->name : $this->guest_name) : $this->guest_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'primary_service_name' => $primaryServiceNameString,
                'additional_services' => !empty($additionalServicesNames) ? $additionalServicesNames : null,
                'appointment_datetime' => $appointmentDateTime,
                'notes' => $this->notes,
                'status' => 'pending', // Estado inicial de la cita.
            ];

            Log::info('Datos para crear SUPER cita:', $appointmentData);
            $superappointment = Superappointment::create($appointmentData); // Crea la cita.

            // Envía correos de notificación.
            Log::info("Enviando correos para SUPERAPPOINTMENT ID {$superappointment->id}");
            Mail::to($superappointment->email)->send(new SuperAppointmentConfirmationToUser($superappointment));
            Mail::to(env('ADMIN_APPOINTMENT_NOTIFICATION_EMAIL'))->send(new NewSuperAppointmentNotificationToAdmin($superappointment));

            $this->successMessage = '¡Tu cita ha sido solicitada con éxito! Recibirás un email con los detalles.';
            Log::info("Solicitud de SUPER cita: ID {$superappointment->id} por {$superappointment->email} para {$superappointment->primary_service_name} el {$appointmentDateTime->format('d/m/Y H:i')}");

            $this->resetFormFields(); // Limpia el formulario.

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si la validación falla (aunque ya se llamó a $this->validate(), puede haber validaciones en el modelo).
            $this->errorMessage = 'Por favor, corrige los errores en el formulario.';
             Log::warning("Error de validación al reservar SUPER cita (submitBooking): " . json_encode($e->errors()));
        } catch (\Exception $e) {
            // Captura cualquier otra excepción.
            Log::error("Error al reservar SUPER cita (submitBooking): {$e->getMessage()} \n{$e->getTraceAsString()}");
            $this->errorMessage = 'Hubo un error inesperado al procesar tu solicitud.';
        }
    }

    /**
     * Resetea los campos del formulario a sus valores iniciales.
     */
    private function resetFormFields()
    {
        // Guarda la clave del servicio de fotocarnet si ese es el contexto.
        $fotocarnetServiceIdKey = null;
        if ($this->pageContext === 'fotocarnet' && isset($this->availablePrimaryServices['fotocarnet_cita'])) {
            $fotocarnetServiceIdKey = 'fotocarnet_cita';
        }

        $this->resetErrorBag(); // Limpia errores de validación de Livewire.
        $this->resetValidation(); // Limpia el estado de validación.
        $this->notes = '';
        $this->appointment_time = '';
        $this->appointment_date = '';
        $this->phone = '';
        $this->selected_additional_services = [];

        // Si el usuario no está logueado, limpia nombre y email.
        if (!Auth::check()) {
            $this->guest_name = '';
            $this->email = '';
        } else { // Si está logueado, restaura su nombre y email.
             $this->guest_name = Auth::user()->name;
             $this->email = Auth::user()->email;
        }

        // Si el contexto es fotocarnet, preselecciona ese servicio; si no, limpia el servicio principal.
        if ($this->pageContext === 'fotocarnet' && $fotocarnetServiceIdKey) {
            $this->primary_service_name = $fotocarnetServiceIdKey;
        } else {
            $this->primary_service_name = '';
        }
        // Las horas disponibles se recalcularán automáticamente.
    }

    // --- Métodos para Administradores ---

    /**
     * Muestra u oculta el panel de configuración de horarios para el administrador.
     */
    public function toggleAdminSettings()
    {
        if ($this->isUserAdmin) {
            $this->showAdminSettings = !$this->showAdminSettings;
            if ($this->showAdminSettings) {
                $this->initializeEditingFields(); // Carga los datos actuales al abrir el panel.
                 $this->resetErrorBag(); // Limpia errores de validación previos del panel de admin.
            }
        }
    }

    /**
     * Guarda la configuración de horarios modificada por el administrador.
     */
    public function saveAdminSettings()
    {
        if (!$this->isUserAdmin) {
            $this->errorMessage = 'No tienes permisos para realizar esta acción.';
            return;
        }

        // Valida los campos del formulario de configuración del admin.
        $validatedData = $this->validate([
            'editingOpeningHour' => 'required|integer|min:0|max:23',
            'editingClosingHour' => 'required|integer|min:0|max:23|gte:editingOpeningHour', // Cierre debe ser >= apertura.
            'editingLunchBreakStartHour' => 'nullable|integer|min:0|max:23',
            'editingLunchBreakEndHour' => 'nullable|integer|min:0|max:23|gte:editingLunchBreakStartHour', // Fin almuerzo >= inicio almuerzo.
            'editingDisabledDatesString' => 'nullable|string', // Validación más robusta del formato de fechas podría ser necesaria.
        ]);

        try {
            // Obtiene la configuración existente o crea una nueva si no existe.
            $settings = BusinessSetting::firstOrCreate(['id' => 1]);

            // Procesa el string de fechas deshabilitadas para convertirlo en un array.
            $disabledDatesArray = [];
            if (!empty($validatedData['editingDisabledDatesString'])) {
                $rawDates = explode(',', $validatedData['editingDisabledDatesString']);
                foreach ($rawDates as $dateStr) {
                    try {
                        // Parsea cada fecha y la formatea a YYYY-MM-DD.
                        $disabledDatesArray[] = Carbon::parse(trim($dateStr))->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Opcional: Añadir error si una fecha no es válida y detener.
                        // $this->addError('editingDisabledDatesString', 'Fecha inválida: ' . trim($dateStr));
                        // return;
                        Log::warning("Formato de fecha inválido en admin settings: " . trim($dateStr));
                    }
                }
                $disabledDatesArray = array_unique($disabledDatesArray); // Elimina duplicados.
                sort($disabledDatesArray); // Ordena las fechas.
            }

            // Actualiza la configuración en la base de datos.
            $settings->update([
                'opening_hour' => $validatedData['editingOpeningHour'],
                'closing_hour' => $validatedData['editingClosingHour'],
                'lunch_start_hour' => $validatedData['editingLunchBreakStartHour'],
                'lunch_end_hour' => $validatedData['editingLunchBreakEndHour'],
                'disabled_dates' => $disabledDatesArray,
                // 'daily_hours' => $this->activeDailyHours, // Si se implementara edición de daily_hours.
            ]);

            $this->loadBusinessSettings(); // Recarga la configuración activa en el componente.
            $this->initializeEditingFields(); // Refresca los campos de edición con los datos guardados.

            $this->successMessage = 'Configuración de horarios guardada correctamente.';
            $this->errorMessage = '';
            $this->showAdminSettings = false; // Opcional: ocultar el panel tras guardar.

            // Si había una fecha seleccionada en el formulario de reserva,
            // fuerza el recálculo de horas disponibles para esa fecha.
            if ($this->appointment_date) {
                $this->updatedAppointmentDate($this->appointment_date);
            }

        } catch (\Exception $e) {
            Log::error("Error al guardar configuración de admin: {$e->getMessage()}");
            $this->errorMessage = 'Error al guardar la configuración: ' . $e->getMessage();
        }
    }

    /**
     * Renderiza la vista del componente.
     * Pasa los datos necesarios a la plantilla Blade.
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // La propiedad computada $this->computedAvailableTimes se actualiza automáticamente.
        // La vista puede acceder a ella directamente o a través de $this->availableTimes si se sincroniza.
        // Para este ejemplo, la vista usará $this->computedAvailableTimes.
        return view('livewire.appointment-scheduler', [
            // No es estrictamente necesario pasar 'currentAvailableTimes' si la vista
            // accede directamente a la propiedad pública computada.
            // 'currentAvailableTimes' => $this->computedAvailableTimes
        ]);
    }
}
