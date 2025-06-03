<div>
    {{-- Sección de Administración de Horarios --}}
    @if($isUserAdmin)
        <div class="mb-6 p-4 bg-sky-50 dark:bg-sky-900 border border-sky-200 dark:border-sky-700 rounded-lg shadow-md">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-sky-800 dark:text-sky-200">Panel de Administrador de Horarios</h3>
                <button wire:click="toggleAdminSettings" class="text-sm font-medium px-3 py-1 rounded-md
                    @if($showAdminSettings) bg-yellow-500 hover:bg-yellow-600 text-white @else bg-sky-600 hover:bg-sky-700 text-white @endif">
                    {{ $showAdminSettings ? 'Ocultar Panel' : 'Configurar Horarios' }}
                </button>
            </div>

            @if($showAdminSettings)
                <form wire:submit.prevent="saveAdminSettings" class="mt-4 space-y-4 pt-4 border-t border-sky-200 dark:border-sky-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Ajusta los horarios globales de atención y fechas específicas no disponibles.
                        Los cambios afectarán la disponibilidad de citas para todos los usuarios.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="editingOpeningHour" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora Apertura Global (0-23)</label>
                            <input type="number" wire:model.lazy="editingOpeningHour" id="editingOpeningHour" min="0" max="23"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('editingOpeningHour') border-red-500 @enderror">
                            @error('editingOpeningHour') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="editingClosingHour" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora Cierre Global (0-23)</label>
                            <input type="number" wire:model.lazy="editingClosingHour" id="editingClosingHour" min="0" max="23"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('editingClosingHour') border-red-500 @enderror">
                            @error('editingClosingHour') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="editingLunchBreakStartHour" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Inicio Almuerzo (0-23, opcional)</label>
                            <input type="number" wire:model.lazy="editingLunchBreakStartHour" id="editingLunchBreakStartHour" min="0" max="23"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('editingLunchBreakStartHour') border-red-500 @enderror">
                            @error('editingLunchBreakStartHour') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="editingLunchBreakEndHour" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fin Almuerzo (0-23, opcional)</label>
                            <input type="number" wire:model.lazy="editingLunchBreakEndHour" id="editingLunchBreakEndHour" min="0" max="23"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('editingLunchBreakEndHour') border-red-500 @enderror">
                            @error('editingLunchBreakEndHour') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label for="editingDisabledDatesString" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fechas No Disponibles (YYYY-MM-DD, separadas por coma)</label>
                        <input type="text" wire:model.lazy="editingDisabledDatesString" id="editingDisabledDatesString" placeholder="Ej: 2025-12-25, 2026-01-01"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('editingDisabledDatesString') border-red-500 @enderror">
                        @error('editingDisabledDatesString') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nota sobre horarios por día --}}
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Para configurar horarios específicos por día de la semana (ej. cerrar los miércoles, o abrir solo mañanas los sábados),
                        actualmente se requiere modificar la base de datos directamente (tabla `business_settings`, columna `daily_hours` en formato JSON).
                        Ejemplo JSON para `daily_hours`: `{"0": {"is_closed": true}, "6": {"open": 9, "close": 14}}` (0=Domingo, 6=Sábado)
                    </p>

                    <div class="flex justify-end mt-6">
                        <button type="submit" wire:loading.attr="disabled"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50">
                            <div wire:loading wire:target="saveAdminSettings" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Guardando...
                            </div>
                            <span wire:loading.remove wire:target="saveAdminSettings">
                                Guardar Configuración Global
                            </span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    @endif

    {{-- Mensajes de Éxito y Error (igual que antes) --}}
    @if($successMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90"
             class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-300 border border-green-200 dark:border-green-700 shadow-md" role="alert">
            <div class="flex items-center">
                <svg class="flex-shrink-0 w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">¡Éxito!</span>
            </div>
            <p class="mt-1.5 ml-7">{{ $successMessage }}</p>
        </div>
    @endif

    @if($errorMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90"
            class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-300 border border-red-200 dark:border-red-700 shadow-md" role="alert">
            <div class="flex items-center">
                <svg class="flex-shrink-0 w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">¡Error!</span>
            </div>
            <p class="mt-1.5 ml-7">{{ $errorMessage }}</p>
        </div>
    @endif

    <form wire:submit.prevent="submitBooking" class="space-y-6">
        {{-- Nombre --}}
        @if(!Auth::check())
            <div>
                <label for="guest_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre Completo <span class="text-red-500">*</span></label>
                <input type="text" wire:model.lazy="guest_name" id="guest_name" autocomplete="name"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('guest_name') border-red-500 dark:border-red-400 @enderror">
                @error('guest_name') <span class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
            </div>
        @else
             <div>
                <label for="guest_name_auth" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tu Nombre (para la cita)</label>
                <input type="text" wire:model.lazy="guest_name" id="guest_name_auth"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('guest_name') border-red-500 dark:border-red-400 @enderror"
                       placeholder="{{ Auth::user()->name }}">
                 @error('guest_name') <span class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
            </div>
        @endif

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo Electrónico <span class="text-red-500">*</span></label>
            <input type="email" wire:model.lazy="email" id="email" autocomplete="email"
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('email') border-red-500 dark:border-red-400 @enderror @if(Auth::check()) dark:bg-gray-600 cursor-not-allowed @endif"
                   @if(Auth::check()) readonly disabled @endif>
            @error('email') <span class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        {{-- Teléfono --}}
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono de Contacto</label>
            <input type="tel" wire:model.lazy="phone" id="phone" autocomplete="tel"
                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('phone') border-red-500 dark:border-red-400 @enderror"
                   placeholder="Ej: +34 600123456">
            @error('phone') <span class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        {{-- Servicio Principal (Select) --}}
        <div>
            <label for="primary_service_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Servicio Principal <span class="text-red-500">*</span></label>
            <select wire:model.live="primary_service_name" id="primary_service_name"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('primary_service_name') border-red-500 dark:border-red-400 @enderror"
                    @if($pageContext === 'fotocarnet' && $primary_service_name === 'fotocarnet_cita') disabled class="dark:bg-gray-600 cursor-not-allowed" @endif>
                @if($pageContext !== 'fotocarnet' || $primary_service_name !== 'fotocarnet_cita')
                    <option value="">Selecciona un servicio principal...</option>
                @endif
                @foreach($availablePrimaryServices as $key => $service)
                    <option value="{{ $service['id'] }}">{{ $service['name'] }} @if($service['duration'] > 0) (aprox. {{ $service['duration'] }} min) @endif</option>
                @endforeach
            </select>
            @error('primary_service_name') <span class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        {{-- Servicios Adicionales (Checkboxes) --}}
        @if(!empty($availableAdditionalServicesList))
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Servicios Adicionales (Opcional)</label>
            <div class="mt-2 space-y-2">
                @foreach($availableAdditionalServicesList as $key => $service)
                    <div class="flex items-center">
                        <input id="additional_service_{{ $service['id'] }}" wire:model.defer="selected_additional_services" type="checkbox" value="{{ $service['id'] }}"
                               class="h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500 dark:bg-gray-700 dark:checked:bg-indigo-500">
                        <label for="additional_service_{{ $service['id'] }}" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            {{ $service['name'] }}
                            @if($service['price'] > 0)
                                (+{{ number_format($service['price'], 2, ',', '.') }}€)
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>
            @error('selected_additional_services') <span class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
            @error('selected_additional_services.*') <span class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
        </div>
        @endif

        {{-- Fecha y Hora --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
            <div>
                <label for="appointment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de la Cita <span class="text-red-500">*</span></label>
                <input type="date" wire:model.live="appointment_date" id="appointment_date"
                       min="{{ $minDate }}" max="{{ $maxDate }}"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('appointment_date') border-red-500 dark:border-red-400 @enderror"
                       @if(empty($primary_service_name)) disabled title="Selecciona primero un servicio principal" class="dark:bg-gray-600 cursor-not-allowed opacity-50" @endif>
                @error('appointment_date') <span class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="appointment_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora Disponible <span class="text-red-500">*</span></label>
                {{-- Usar $this->computedAvailableTimes directamente o una variable que se actualice de forma fiable --}}
                @php $currentTimes = $this->computedAvailableTimes; @endphp
                <select wire:model.defer="appointment_time" id="appointment_time"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('appointment_time') border-red-500 dark:border-red-400 @enderror @if(empty($currentTimes) || empty($appointment_date) || $errors->has('appointment_date') || empty($primary_service_name)) dark:bg-gray-600 cursor-not-allowed opacity-50 @endif"
                        @if(empty($currentTimes) || empty($appointment_date) || $errors->has('appointment_date') || empty($primary_service_name)) disabled title="Selecciona una fecha y servicio válidos primero" @endif>
                    <option value="">Selecciona una hora...</option>
                    @if(!empty($currentTimes))
                        @foreach($currentTimes as $timeValue => $timeLabel)
                            <option value="{{ $timeValue }}">{{ $timeLabel }}</option>
                        @endforeach
                    @elseif(!empty($appointment_date) && !$errors->has('appointment_date') && !empty($primary_service_name) && !$errors->has('appointment_time'))
                        {{-- Este elseif puede ser redundante si el error de 'appointment_time' ya cubre esto --}}
                        <option value="" disabled>No hay horas para esta fecha/servicio.</option>
                    @endif
                </select>
                @error('appointment_time') <span class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
                 <div wire:loading wire:target="appointment_date, primary_service_name" class="mt-1 text-xs text-indigo-500 dark:text-indigo-400 flex items-center">
                    <svg class="animate-spin h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Buscando horas...
                </div>
            </div>
        </div>

        {{-- Notas --}}
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notas Adicionales (Opcional)</label>
            <textarea wire:model.defer="notes" id="notes" rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('notes') border-red-500 dark:border-red-400 @enderror"
                      placeholder="Ej: Vengo con bebé para fotocarnet, necesito factura, etc."></textarea>
            @error('notes') <span class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        {{-- Botón de Envío --}}
        <div>
            <button type="submit" wire:loading.attr="disabled" wire:target="submitBooking"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-900 focus:ring-indigo-500 disabled:opacity-70 transition-opacity">
                <div wire:loading.flex wire:target="submitBooking" class="items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando Solicitud...
                </div>
                <span wire:loading.remove wire:target="submitBooking">
                    Solicitar Cita
                </span>
            </button>
        </div>
    </form>
</div>
