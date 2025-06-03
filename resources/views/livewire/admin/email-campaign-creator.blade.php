{{-- resources/views/livewire/admin/email-campaign-creator.blade.php --}}
<div> {{-- <<< ELEMENTO RAÍZ OBLIGATORIO PARA Livewire --}}

    @if($showCampaignInterfaceModal)
        <div class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="main-campaign-modal-title"
             role="dialog" aria-modal="true" x-cloak>
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

                {{-- Fondo semitransparente --}}
                <div wire:click="closeInterfaceModal"
                     class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                     aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Panel del modal principal --}}
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left
                            overflow-hidden shadow-2xl transform transition-all
                            sm:my-8 sm:align-middle
                            w-full max-w-md              {{-- Pantallas pequeñas: ancho medio --}}
                            md:max-w-3xl                 {{-- A partir md: 3xl --}}
                            lg:max-w-5xl                 {{-- A partir lg: 5xl --}}
                            xl:max-w-6xl                 {{-- A partir xl: 6xl --}}
                            dark:border dark:border-gray-700">

                    {{-- Cabecera (header) del modal --}}
                    <div class="flex justify-between items-center px-8 py-4 bg-gray-50 dark:bg-gray-900
                                border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
                        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200"
                            id="main-campaign-modal-title">
                            Gestor de Campañas de Email
                        </h2>
                        <button wire:click="closeInterfaceModal" type="button"
                                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Contenido principal --}}
                    <div class="px-8 py-6 max-h-[85vh] overflow-y-auto"
                         x-data="{
                            showNotification: false,
                            notificationMessage: '',
                            notificationType: '',
                            init() {
                                Livewire.on('notification-message', () => {
                                    this.notificationMessage = @this.get('notificationMessage');
                                    this.notificationType = @this.get('notificationType');
                                    this.showNotification = true;
                                    setTimeout(() => { this.showNotification = false }, 5000);
                                });
                                Livewire.on('trix-input', (eventDetail) => {
                                    let trixEditor = document.querySelector(`trix-editor[input=${eventDetail.id}]`);
                                    if (trixEditor && trixEditor.editor) {
                                        trixEditor.editor.loadHTML(eventDetail.content || '');
                                    } else {
                                        setTimeout(() => {
                                            trixEditor = document.querySelector(`trix-editor[input=${eventDetail.id}]`);
                                            if (trixEditor && trixEditor.editor) {
                                                trixEditor.editor.loadHTML(eventDetail.content || '');
                                            }
                                        }, 150);
                                    }
                                });
                                Livewire.on('trix-clear', (inputId) => {
                                    let trixEditor = document.querySelector(`trix-editor[input=${inputId}]`);
                                    if (trixEditor && trixEditor.editor) {
                                        trixEditor.editor.loadHTML('');
                                    }
                                });
                            }
                         }">

                        {{-- Botón “Crear Nueva Campaña” --}}
                        <div class="flex justify-end mb-6">
                            <button wire:click="openCreateEditFormModal" type="button"
                                    class="inline-flex items-center px-5 py-3 bg-indigo-600 text-white
                                           rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2
                                           focus:ring-indigo-500 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2"
                                     viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M10 3a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0
                                             110-2h4V4a1 1 0 011-1z"
                                          clip-rule="evenodd" />
                                </svg>
                                Crear Nueva Campaña
                            </button>
                        </div>

                        {{-- Notificaciones (toast) --}}
                        <div x-show="showNotification" x-cloak
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             :class="{
                                'bg-green-50 border-l-4 border-green-500 text-green-700': notificationType === 'success',
                                'bg-red-50 border-l-4 border-red-500 text-red-700': notificationType === 'error',
                                'bg-blue-50 border-l-4 border-blue-500 text-blue-700': notificationType === 'info',
                             }"
                             class="fixed top-6 right-6 z-30 max-w-sm p-4 text-sm rounded shadow-lg">
                            <div class="flex items-start">
                                <span class="flex-1 font-medium"
                                      x-text="notificationType === 'success'
                                             ? '¡Éxito!'
                                             : (notificationType === 'error'
                                                ? '¡Error!'
                                                : 'Información:')">
                                </span>
                                <button @click="showNotification = false"
                                        class="ml-2 text-lg font-semibold leading-none hover:opacity-75">&times;
                                </button>
                            </div>
                            <p class="mt-1" x-text="notificationMessage"></p>
                        </div>

                      {{-- ─── SUB-MODAL: “Crear/Editar Campaña” ──────────────────────────────────────────── --}}
@if($showCreateEditFormModal)
    <div class="fixed inset-0 z-40 overflow-y-auto"
         aria-labelledby="campaign-form-modal-title"
         role="dialog" aria-modal="true" x-cloak>
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Backdrop --}}
            <div wire:click="closeCreateEditFormModal"
                 class="fixed inset-0 bg-black bg-opacity-40 transition-opacity dark:bg-black dark:bg-opacity-60"
                 aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Panel del sub-modal --}}
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left
                        overflow-hidden shadow-xl transform transition-all
                        sm:my-8 sm:align-middle sm:w-full sm:max-w-3xl max-h-[90vh]
                        dark:border dark:border-gray-700">
                <form wire:submit.prevent="saveCampaign">
                    {{-- Encabezado del sub-modal --}}
                    <div class="bg-gray-100 dark:bg-gray-900 px-6 py-4 border-b dark:border-gray-700 sticky top-0 z-10">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100"
                            id="campaign-form-modal-title">
                            {{ $editingCampaignId ? 'Editar Campaña' : 'Crear Nueva Campaña' }}
                        </h3>
                    </div>

                    {{-- Cuerpo (scroll) --}}
                    <div class="px-6 py-4 overflow-y-auto" style="max-height: calc(90vh - 140px);">

                        {{-- ────────────── FILA COMPLETA: Editor Trix (barra + área) ────────────── --}}
                        <div class="mb-6">
                            <label for="email_body_html_input_new_campaign"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Cuerpo del Email (HTML)
                            </label>
                            {{-- Input hidden para Trix --}}
                            <input id="email_body_html_input_new_campaign" type="hidden"
                                   name="content" value="{{ $email_body_html }}">
                            <trix-editor input="email_body_html_input_new_campaign"
                                         class="mt-1 trix-content block w-full rounded-md border-gray-300
                                                dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white
                                                prose max-w-none dark:prose-invert min-h-[200px]"
                                         x-on:trix-change.debounce.500ms="$wire.set('email_body_html', $event.target.value)">
                            </trix-editor>
                            @error('email_body_html')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        {{-- ────────────────────────────────────────────────────────────────── --}}

                        {{-- Ahora dividimos en dos columnas: izquierda (detalles) y derecha (destinatarios) --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            {{-- Columna IZQUIERDA: Detalles de la campaña --}}
                            <div class="space-y-5">
                                {{-- Nombre campaña --}}
                                <div>
                                    <label for="campaign_name"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nombre de la Campaña (interno)
                                    </label>
                                    <input type="text" wire:model.lazy="campaign_name" id="campaign_name"
                                           class="mt-1 block w-full rounded-md border-gray-300
                                                  dark:border-gray-600 shadow-sm dark:bg-gray-700
                                                  dark:text-white sm:text-sm @error('campaign_name') border-red-500 @enderror">
                                    @error('campaign_name')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Oferta asociada --}}
                                <div>
                                    <label for="offer_id"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Oferta Asociada
                                    </label>
                                    <select wire:model.defer="offer_id" id="offer_id"
                                            class="mt-1 block w-full rounded-md border-gray-300
                                                   dark:border-gray-600 shadow-sm dark:bg-gray-700
                                                   dark:text-white sm:text-sm @error('offer_id') border-red-500 @enderror">
                                        <option value="">-- Sin Oferta Asociada --</option>
                                        @if($offers && $offers->isNotEmpty())
                                            @foreach($offers as $offer)
                                                <option value="{{ $offer->id }}">
                                                    {{ $offer->name ?? $offer->title ?? 'ID ' . $offer->id }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>No hay ofertas</option>
                                        @endif
                                    </select>
                                    @error('offer_id')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Asunto del email --}}
                                <div>
                                    <label for="email_subject"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Asunto del Email
                                    </label>
                                    <input type="text" wire:model.lazy="email_subject" id="email_subject"
                                           class="mt-1 block w-full rounded-md border-gray-300
                                                  dark:border-gray-600 shadow-sm dark:bg-gray-700
                                                  dark:text-white sm:text-sm @error('email_subject') border-red-500 @enderror">
                                    @error('email_subject')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Fecha y hora de envío --}}
                                <div>
                                    <label for="date_of_send"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Fecha y Hora de Envío
                                    </label>
                                    <input type="datetime-local" wire:model.defer="date_of_send"
                                           id="date_of_send"
                                           class="mt-1 block w-full rounded-md border-gray-300
                                                  dark:border-gray-600 shadow-sm dark:bg-gray-700
                                                  dark:text-white sm:text-sm @error('date_of_send') border-red-500 @enderror">
                                    @error('date_of_send')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Archivos adjuntos --}}
                                <div>
                                    <label for="attachments_input"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Archivos Adjuntos (max. 5MB c/u)
                                    </label>
                                    <input type="file" wire:model="attachments" id="attachments_input" multiple
                                           class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300
                                                  file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                                                  file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-800
                                                  file:text-indigo-700 dark:file:text-indigo-200 hover:file:bg-indigo-100
                                                  dark:hover:file:bg-indigo-700">
                                    @error('attachments.*')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                    <div wire:loading wire:target="attachments"
                                         class="mt-1 text-xs text-gray-500">
                                        Subiendo archivos...
                                    </div>
                                    {{-- Previsualización de archivos --}}
                                    @if($attachments && count(array_filter((array)$attachments)) > 0)
                                        <div class="mt-2 text-sm">
                                            <p>Archivos a subir:</p>
                                            <ul class="max-h-20 overflow-y-auto">
                                                @foreach(array_filter((array)$attachments) as $idx => $file)
                                                    @if(is_object($file) && method_exists($file, 'getClientOriginalName'))
                                                        <li class="flex justify-between items-center py-0.5">
                                                            <span class="truncate">
                                                                {{ $file->getClientOriginalName() }}
                                                                ({{ round($file->getSize()/1024, 2) }} KB)
                                                            </span>
                                                            <button type="button"
                                                                    wire:click="removeAttachment({{ $idx }})"
                                                                    class="ml-2 text-red-500 hover:text-red-700 text-xs font-semibold">
                                                                &times; Quitar
                                                            </button>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- Adjuntos existentes (si editamos) --}}
                                    @if($editingCampaignId && $campaignToEdit && $campaignToEdit->attachment_paths && count($campaignToEdit->attachment_paths) > 0)
                                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            <p class="font-medium">
                                                Adjuntos actuales (al subir nuevos se reemplazarán):
                                            </p>
                                            <ul class="list-disc pl-5 max-h-20 overflow-y-auto">
                                                @foreach($campaignToEdit->attachment_paths as $existingPath)
                                                    <li>{{ basename($existingPath) }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Columna DERECHA: Selección de destinatarios --}}
                            <div class="space-y-5">
                                {{-- Usuarios registrados --}}
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <h4 class="mb-2 font-medium text-gray-800 dark:text-gray-200">
                                        Usuarios Registrados
                                    </h4>
                                    <input type="text"
                                           wire:model.live.debounce.300ms="searchTermUsers"
                                           placeholder="Buscar usuario..."
                                           class="mb-2 block w-full text-sm rounded-md border-gray-300
                                                  dark:border-gray-600 shadow-sm dark:bg-gray-600 dark:text-white">
                                    <div class="max-h-40 overflow-y-auto border dark:border-gray-600 rounded-md p-2 space-y-1">
                                        @if($availableUsers && $availableUsers->isNotEmpty())
                                            @foreach($availableUsers as $user)
                                                <label class="flex items-center text-sm py-1 hover:bg-gray-100 dark:hover:bg-gray-600/50 px-1 rounded-lg">
                                                    <input type="checkbox"
                                                           wire:model.defer="recipient_source_users"
                                                           value="{{ $user->id }}"
                                                           class="rounded border-gray-300 dark:border-gray-600
                                                                  text-indigo-600 shadow-sm focus:border-indigo-300
                                                                  focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                                                  dark:bg-gray-700 dark:checked:bg-indigo-500">
                                                    <span class="ml-2 text-gray-700 dark:text-gray-300 truncate">
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </span>
                                                </label>
                                            @endforeach
                                        @else
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                No hay usuarios disponibles.
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Invitados (correos) --}}
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <h4 class="mb-2 font-medium text-gray-800 dark:text-gray-200">
                                        Correos de Invitados (Citas)
                                    </h4>
                                    <input type="text"
                                           wire:model.live.debounce.300ms="searchTermGuests"
                                           placeholder="Buscar email invitado..."
                                           class="mb-2 block w-full text-sm rounded-md border-gray-300
                                                  dark:border-gray-600 shadow-sm dark:bg-gray-600 dark:text-white">
                                    <div class="max-h-40 overflow-y-auto border dark:border-gray-600 rounded-md p-2 space-y-1">
                                        @if($availableGuestEmails && $availableGuestEmails->isNotEmpty())
                                            @foreach($availableGuestEmails as $guestEmail)
                                                <label class="flex items-center text-sm py-1 hover:bg-gray-100 dark:hover:bg-gray-600/50 px-1 rounded-lg">
                                                    <input type="checkbox"
                                                           wire:model.defer="recipient_source_guests"
                                                           value="{{ $guestEmail }}"
                                                           class="rounded border-gray-300 dark:border-gray-600
                                                                  text-indigo-600 shadow-sm focus:border-indigo-300
                                                                  focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                                                  dark:bg-gray-700 dark:checked:bg-indigo-500">
                                                    <span class="ml-2 text-gray-700 dark:text-gray-300 truncate">
                                                        {{ $guestEmail }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        @else
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                No hay invitados disponibles.
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Emails manuales --}}
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <label for="manual_emails_text"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Añadir Emails Manualmente
                                    </label>
                                    <textarea wire:model.lazy="manual_emails_text"
                                              wire:blur="parseManualEmails"
                                              id="manual_emails_text" rows="3"
                                              placeholder="Sepáralos por coma, espacio o punto y coma..."
                                              class="mt-1 block w-full rounded-md border-gray-300
                                                     dark:border-gray-600 shadow-sm dark:bg-gray-700
                                                     dark:text-white sm:text-sm"></textarea>
                                    @if(count($parsed_manual_emails) > 0)
                                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                            Detectados: {{ count($parsed_manual_emails) }} emails
                                        </p>
                                    @endif
                                </div>
                                @error('final_recipient_list')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                        </div> {{-- Fin grid-cols-2 --}}
                    </div> {{-- Fin cuerpo scrollable --}}

                    {{-- Footer del sub-modal: Botones Guardar / Cancelar --}}
                    <div class="bg-gray-100 dark:bg-gray-900 px-6 py-4 border-t dark:border-gray-700
                                sm:flex sm:flex-row-reverse sticky bottom-0 z-10">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:target="saveCampaign, attachments"
                                class="w-full sm:w-auto inline-flex justify-center px-6 py-3
                                       bg-indigo-600 text-white rounded-lg hover:bg-indigo-700
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                                       transition sm:ml-3 mb-2 sm:mb-0 text-sm font-medium">
                            <span wire:loading.remove wire:target="saveCampaign, attachments">
                                {{ $editingCampaignId ? 'Actualizar Campaña' : 'Guardar y Programar' }}
                            </span>
                            <svg wire:loading wire:target="saveCampaign, attachments"
                                 class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                                 xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0
                                         0 5.373 0 12h4zm2 5.291A7.962 7.962 0
                                         014 12H0c0 3.042 1.135 5.824
                                         3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                        <button wire:click="closeCreateEditFormModal" type="button"
                                class="w-full sm:w-auto inline-flex justify-center px-6 py-3
                                       bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200
                                       rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600
                                       focus:outline-none focus:ring-2 focus:ring-offset-2
                                       focus:ring-indigo-500 transition text-sm font-medium">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
{{-- ─── FIN SUB-MODAL ──────────────────────────────────────────────────── --}}

                        {{-- ─── TABLA CON CAMPAÑAS EXISTENTES ─────────────────────────────────────────────── --}}
                        <div class="mt-8 overflow-x-auto bg-white dark:bg-gray-800 shadow-lg rounded-lg">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 p-6 border-b dark:border-gray-700">
                                Campañas Programadas y Anteriores
                            </h3>
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Nombre Campaña
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Oferta
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Asunto
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Fecha Envío
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @if($campaigns && $campaigns->isNotEmpty())
                                        @foreach($campaigns as $campaign)
                                            <tr wire:key="campaign-{{ $campaign->id }}">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $campaign->campaign_name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                    {{ $campaign->offer->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                    {{ Str::limit($campaign->email_subject, 30) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                    {{ \Carbon\Carbon::parse($campaign->date_of_send)->translatedFormat('d/m/Y H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span @class([
                                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' => $campaign->status === 'pending',
                                                        'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'  => $campaign->status === 'sent',
                                                        'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'         => $campaign->status === 'failed',
                                                        'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100'     => !in_array($campaign->status, ['pending','sent','failed']),
                                                    ])>
                                                        {{ ucfirst($campaign->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                                    {{-- Botón Editar --}}
                                                    <button wire:click="editCampaign({{ $campaign->id }})"
                                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200"
                                                            title="Editar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline"
                                                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M15.232 5.232l3.536 3.536m-2.036-5.036
                                                                     a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572
                                                                     L16.732 3.732z" />
                                                        </svg>
                                                    </button>
                                                    {{-- Botón Enviar Ahora (si pendiente) --}}
                                                    @if($campaign->status === 'pending')
                                                        <button wire:click="sendCampaignNow({{ $campaign->id }})"
                                                                wire:confirm="Simulación: se enviarán correos. ¿Continuar?"
                                                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-200"
                                                                title="Enviar Ahora">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline"
                                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                    {{-- Botón Eliminar --}}
                                                    <button wire:click="deleteCampaign({{ $campaign->id }})"
                                                            wire:confirm="¿Eliminar esta campaña? Esta acción no se puede deshacer."
                                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200"
                                                            title="Eliminar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline"
                                                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                                                     a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4
                                                                     a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No hay campañas creadas todavía. Haz clic en “Crear Nueva Campaña” para empezar.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            @if($campaigns && $campaigns->hasPages())
                                <div class="p-4">
                                    {{ $campaigns->links() }}
                                </div>
                            @endif
                        </div>
                    </div> {{-- Fin contenido principal --}}
                </div> {{-- Fin panel modal principal --}}
            </div>
        </div>
    @endif

</div> {{-- <<< FIN ELEMENTO RAÍZ --}}
