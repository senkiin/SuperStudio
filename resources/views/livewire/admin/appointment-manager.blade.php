<div class="p-4 sm:p-6 lg:p-8 bg-gray-100 dark:bg-gray-900 min-h-screen" x-data>
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Gestor de Citas</h2>

    {{-- Notificaciones Flash --}}
    @if($notificationMessage)
        <div x-data="{ showNotification: true }" x-show="showNotification" x-init="setTimeout(() => showNotification = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             @class([
                'fixed top-5 right-5 z-[60] w-auto max-w-sm p-4 text-sm rounded-lg shadow-lg',
                'bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 border border-green-300 dark:border-green-600' => $notificationType === 'success',
                'bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 border border-red-300 dark:border-red-600' => $notificationType === 'error',
             ])
             role="alert">
            <span class="font-medium">{{ $notificationType === 'success' ? '¡Éxito!' : '¡Error!' }}</span> {{ $notificationMessage }}
            <button @click="showNotification = false" class="ml-4 text-lg font-semibold leading-none hover:text-opacity-75">&times;</button>
        </div>
    @endif

    {{-- Filtros y Búsqueda --}}
    <div class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="searchTerm" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar Cita</label>
                <input type="text" wire:model.live.debounce.300ms="searchTerm" id="searchTerm" placeholder="Nombre, email, servicio..."
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm">
            </div>
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por Estado</label>
                <select wire:model.live="statusFilter" id="statusFilter"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                    <option value="">Todos los Estados</option>
                    <option value="pending">Pendiente</option>
                    <option value="confirmed">Confirmada</option>
                    <option value="cancelled">Cancelada</option>
                    <option value="completed">Completada</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Lista de Citas --}}
    <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contacto</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Servicio</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha y Hora</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($appointments_paginated as $appointment)
                    <tr wire:key="appointment-row-{{ $appointment->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $appointment->guest_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            <div>{{ $appointment->email }}</div>
                            <div>{{ $appointment->phone ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ Str::limit($appointment->primary_service_name, 30) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->translatedFormat('d/m/y H:i') }}
                            ({{ \Carbon\Carbon::parse($appointment->appointment_datetime)->diffForHumans() }})
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $statusClasses = [
                                    'pending'   => 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-700 dark:text-yellow-100 dark:border-yellow-600',
                                    'confirmed' => 'bg-green-100 text-green-800 border-green-300 dark:bg-green-700 dark:text-green-100 dark:border-green-600',
                                    'cancelled' => 'bg-red-100 text-red-800 border-red-300 dark:bg-red-700 dark:text-red-100 dark:border-red-600',
                                    'completed' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-700 dark:text-blue-100 dark:border-blue-600',
                                    'default'   => 'bg-gray-100 text-gray-800 border-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500',
                                ];
                                $currentStatusClass = $statusClasses[$appointment->status] ?? $statusClasses['default'];
                            @endphp
                            <select wire:change="updateAppointmentStatus({{ $appointment->id }}, $event.target.value)"
                                    title="Cambiar estado de la cita"
                                    class="text-xs p-1.5 border focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm dark:bg-gray-900 dark:text-white {{ $currentStatusClass }} appearance-none leading-tight w-full sm:w-auto">
                                <option value="pending" @if($appointment->status == 'pending') selected @endif>Pendiente</option>
                                <option value="confirmed" @if($appointment->status == 'confirmed') selected @endif>Confirmada</option>
                                <option value="cancelled" @if($appointment->status == 'cancelled') selected @endif>Cancelada</option>
                                <option value="completed" @if($appointment->status == 'completed') selected @endif>Completada</option>
                            </select>
                            <div wire:loading wire:target="updateAppointmentStatus({{ $appointment->id }})" class="inline-block ml-2 align-middle">
                                <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button wire:click="openAppointmentModal({{ $appointment->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200" title="Ver Detalles y Gestionar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                            </button>
                            <button wire:click="openEmailModal({{ $appointment->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-200" title="Enviar Correo">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" /><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" /></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            No se encontraron citas con los filtros actuales.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $appointments_paginated->links() }}
    </div>


    {{-- Modal Detalles de Cita y Gestión --}}
    @if($showModal && $selectedAppointment)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
        x-data="{ showModal: @entangle('showModal') }" x-show="showModal" x-cloak
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Fondo oscuro del modal --}}
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 wire:click="closeModals" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-gray-900 dark:bg-opacity-75" aria-hidden="true"></div>

            {{-- Centrador vertical --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Contenido del Modal --}}
            <div x-show="showModal" x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-700 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                Detalles de la Cita
                            </h3>
                            <div class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                                <p><strong>ID Cita:</strong> {{ $selectedAppointment->id }}</p>
                                <p><strong>Cliente:</strong> {{ $selectedAppointment->guest_name }}</p>
                                <p><strong>Email:</strong> {{ $selectedAppointment->email }}</p>
                                <p><strong>Teléfono:</strong> {{ $selectedAppointment->phone ?? 'N/A' }}</p>
                                <p><strong>Servicio:</strong> {{ $selectedAppointment->primary_service_name }}</p>
                                @if($selectedAppointment->additional_services && (is_array($selectedAppointment->additional_services) ? count($selectedAppointment->additional_services) : !empty($selectedAppointment->additional_services)))
                                <p><strong>Serv. Adicionales:</strong> {{ is_array($selectedAppointment->additional_services) ? implode(', ', $selectedAppointment->additional_services) : $selectedAppointment->additional_services }}</p>
                                @endif
                                <p><strong>Fecha y Hora:</strong> {{ \Carbon\Carbon::parse($selectedAppointment->appointment_datetime)->translatedFormat('l, d \d\e F \d\e Y \a \l\a\s H:i') }}</p>
                                <p><strong>Estado Actual:</strong> <span class="font-semibold">{{ ucfirst($selectedAppointment->status) }}</span></p>
                                @if($selectedAppointment->notes)
                                <p class="mt-2"><strong>Notas del Cliente:</strong></p>
                                <p class="p-2 bg-gray-50 dark:bg-gray-700 rounded whitespace-pre-wrap border border-gray-200 dark:border-gray-600">{{ $selectedAppointment->notes }}</p>
                                @endif
                            </div>

                            <div class="mt-6 border-t dark:border-gray-700 pt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cambiar Estado (desde modal):</label>
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="updateAppointmentStatus({{ $selectedAppointment->id }}, 'confirmed')" @if($selectedAppointment->status == 'confirmed') disabled @endif
                                        class="px-3 py-1 text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">Confirmar</button>
                                    <button wire:click="updateAppointmentStatus({{ $selectedAppointment->id }}, 'completed')" @if($selectedAppointment->status == 'completed') disabled @endif
                                        class="px-3 py-1 text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">Completada</button>
                                    <button wire:click="updateAppointmentStatus({{ $selectedAppointment->id }}, 'cancelled')" @if($selectedAppointment->status == 'cancelled') disabled @endif
                                        class="px-3 py-1 text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="openEmailModal({{ $selectedAppointment->id }})" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Contactar Cliente
                    </button>
                    <button wire:click="closeModals" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Enviar Correo --}}
    @if($showEmailModal)
    <div class="fixed inset-0 z-[55] overflow-y-auto" aria-labelledby="email-modal-title" role="dialog" aria-modal="true"
        x-data="{ showEmailModal: @entangle('showEmailModal') }" x-show="showEmailModal" x-cloak
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEmailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 wire:click="closeModals" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-gray-900 dark:bg-opacity-75" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEmailModal" x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <form wire:submit.prevent="sendCustomEmail">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-700 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="email-modal-title">
                                    Enviar Correo a Cliente
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="emailTo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Para:</label>
                                        <input type="email" wire:model.lazy="emailTo" id="emailTo" readonly
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-300 sm:text-sm cursor-not-allowed">
                                        @error('emailTo') <span class="mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="emailSubject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asunto:</label>
                                        <input type="text" wire:model.lazy="emailSubject" id="emailSubject"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('emailSubject') border-red-500 @enderror">
                                        @error('emailSubject') <span class="mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="emailBody" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mensaje:</label>
                                        <textarea wire:model.lazy="emailBody" id="emailBody" rows="6"
                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('emailBody') border-red-500 @enderror"
                                                  placeholder="Escribe tu mensaje aquí..."></textarea>
                                        @error('emailBody') <span class="mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" wire:loading.attr="disabled" wire:target="sendCustomEmail"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span wire:loading.remove wire:target="sendCustomEmail">Enviar Correo</span>
                            <svg wire:loading wire:target="sendCustomEmail" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                        <button wire:click="closeModals" type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
