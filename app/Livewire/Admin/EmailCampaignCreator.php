<?php

namespace App\Livewire\Admin;

// Importaciones de Modelos y Fachadas de Laravel/Livewire
use App\Models\EmailCampaign; // Modelo para las campañas de email
use App\Models\Offer;         // Modelo para las ofertas (si se asocian a campañas)
use App\Models\User;          // Modelo para los usuarios (destinatarios)
use App\Models\Superappointment; // Modelo para obtener emails de invitados de citas
use Livewire\Component;       // Clase base para componentes Livewire
use Livewire\WithFileUploads; // Trait para manejar subida de archivos
use Livewire\WithPagination;  // Trait para paginación fácil
use Illuminate\Support\Facades\Storage; // Fachada para interactuar con el sistema de archivos (ej. S3)
use Illuminate\Support\Facades\Mail;    // Fachada para enviar correos
use Illuminate\Support\Facades\Log;     // Fachada para registrar logs
use Illuminate\Support\Str;       // Helper para manipulación de strings (ej. slugs)
use Carbon\Carbon;              // Librería para manejo avanzado de fechas y horas
use Illuminate\Support\Facades\Schema; // Fachada Schema para interactuar con la estructura de la base de datos

class EmailCampaignCreator extends Component
{
    use WithFileUploads; // Habilita la subida de archivos en el componente
    use WithPagination;  // Habilita la paginación para listas (ej. campañas existentes)

    // --- Control de Modales ---
    public $showCampaignInterfaceModal = false; // Controla la visibilidad del modal principal del gestor de campañas

    // --- Propiedades del Formulario de Campaña (usadas en el modal interno de creación/edición) ---
    public string $campaign_name = ''; // Nombre interno de la campaña
    public $offer_id = null;           // ID de la oferta asociada (opcional)
    public string $email_subject = ''; // Asunto del correo electrónico
    public string $email_body_html = ''; // Contenido HTML del correo (se enlazará con el editor Trix)
    public $date_of_send;              // Fecha y hora programada para el envío
    public $attachments = [];          // Array para almacenar archivos adjuntos subidos temporalmente

    // --- Gestión de Destinatarios ---
    public array $recipient_source_users = [];  // IDs de usuarios registrados seleccionados como destinatarios
    public array $recipient_source_guests = []; // Emails de invitados (de Superappointment) seleccionados
    public string $manual_emails_text = '';    // Campo de texto para añadir emails manualmente, separados por comas/espacios/etc.
    public array $parsed_manual_emails = [];    // Emails extraídos y validados del campo manual_emails_text
    public array $final_recipient_list = [];    // Lista consolidada y única de correos electrónicos para la campaña

    // --- Datos para Selectores y Listas (precargados o cargados dinámicamente) ---
    public $offers = []; // Colección de ofertas disponibles para asociar
    public $users;       // Colección de usuarios (se carga al abrir el modal o al buscar)
    public $guest_emails; // Array de emails de invitados (se carga al abrir el modal o al buscar)

    // --- Estado de Edición ---
    public $editingCampaignId = null; // ID de la campaña que se está editando (null si es nueva)
    public $campaignToEdit;           // Instancia del modelo EmailCampaign al editar

    // --- UI y Notificaciones ---
    public $showCreateEditFormModal = false; // Controla el modal interno para crear/editar una campaña específica
    public string $searchTermUsers = '';     // Término de búsqueda para la lista de usuarios
    public string $searchTermGuests = '';    // Término de búsqueda para la lista de emails de invitados
    public string $notificationMessage = ''; // Mensaje para notificaciones flash
    public string $notificationType = '';    // Tipo de notificación: 'success', 'error', 'info'

    // Listener para eventos de Livewire. Aquí, escucha un evento para abrir el modal principal.
    protected $listeners = ['openEmailCampaignModalEvent' => 'openInterfaceModal'];

    // Define las reglas de validación para el formulario de creación/edición de campañas.
    protected function rules()
    {
        return [
            'campaign_name' => 'required|string|max:255', // Nombre de campaña es obligatorio
            'offer_id' => 'nullable|sometimes|exists:offers,id', // ID de oferta es opcional, pero si se envía, debe existir en la tabla 'offers'
            'email_subject' => 'required|string|max:255', // Asunto es obligatorio
            'email_body_html' => 'required|string|min:50', // Cuerpo del email es obligatorio y con mínimo de caracteres
            'date_of_send' => 'required|date|after_or_equal:' . Carbon::today()->toDateString(), // Fecha de envío obligatoria, hoy o futura
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:5120', // Validación para cada adjunto (opcional, tipos y tamaño)
            'recipient_source_users' => 'nullable|array', // Lista de IDs de usuarios (opcional)
            'recipient_source_guests' => 'nullable|array', // Lista de emails de invitados (opcional)
            'manual_emails_text' => 'nullable|string', // Emails manuales (opcional)
        ];
    }

    // Define mensajes personalizados para las reglas de validación.
    protected function messages() {
        return [
            'campaign_name.required' => 'El nombre de la campaña es obligatorio.',
            'email_subject.required' => 'El asunto del email es obligatorio.',
            'email_body_html.required' => 'El contenido del email es obligatorio.',
            'email_body_html.min' => 'El contenido del email debe tener al menos 50 caracteres.',
            'date_of_send.required' => 'La fecha de envío es obligatoria.',
            'date_of_send.after_or_equal' => 'La fecha de envío debe ser hoy o una fecha futura.',
            'attachments.*.mimes' => 'El archivo adjunto debe ser de tipo: pdf, doc, docx, jpg, jpeg, png, gif.',
            'attachments.*.max' => 'Cada archivo adjunto no debe superar los 5MB.',
        ];
    }

    // Método `mount`: se ejecuta una vez cuando el componente se inicializa.
    public function mount()
    {
        try {
            // Verifica si la tabla 'offers' existe para evitar errores si no está migrada.
            if (Schema::hasTable('offers')) {
                $query = Offer::query();
                // Intenta ordenar por 'name', luego por 'title', o solo tomar IDs.
                if (Schema::hasColumn('offers', 'name')) {
                    $query->orderBy('name');
                    $this->offers = $query->get(['id', 'name']);
                } elseif (Schema::hasColumn('offers', 'title')) {
                    $query->orderBy('title');
                    $this->offers = $query->select('id', 'title as name')->get(); // Alias 'title' como 'name' para consistencia
                } elseif (Schema::hasColumn('offers', 'id')) {
                    $this->offers = $query->get(['id']);
                    Log::warning("Tabla 'offers' no tiene columna 'name' o 'title'. Cargando solo IDs para ofertas.");
                } else {
                    $this->offers = collect(); // Colección vacía si no hay columnas usables
                    Log::warning("Tabla 'offers' no tiene columnas 'id', 'name', o 'title'. Las ofertas no se cargarán.");
                }
            } else {
                $this->offers = collect(); // Colección vacía si la tabla no existe
                Log::info("La tabla 'offers' no existe. Las ofertas no se cargarán para campañas.");
            }
        } catch (\Exception $e) {
            // Captura cualquier excepción durante la carga de ofertas y la registra.
            Log::error("Error crítico al cargar ofertas en EmailCampaignCreator: " . $e->getMessage());
            $this->offers = collect(); // Asegura que $offers sea una colección vacía en caso de error.
        }
        // Inicializa la fecha de envío por defecto al día siguiente, a la hora en punto.
        $this->date_of_send = Carbon::now()->addDay()->startOfHour()->format('Y-m-d\TH:i');
    }

    // Abre el modal principal de la interfaz de campañas.
    public function openInterfaceModal()
    {
        // Carga la lista de usuarios si aún no se ha hecho (carga perezosa).
        if ($this->users === null) {
             $this->users = User::where('role', 'user')->orderBy('name')->limit(100)->get(['id', 'name', 'email']);
        }
        // Carga la lista de emails de invitados si aún no se ha hecho.
        if ($this->guest_emails === null) {
            $this->guest_emails = Superappointment::whereNotNull('email')
                                ->distinct() // Solo emails únicos
                                ->orderBy('email')
                                ->limit(100)
                                ->pluck('email') // Obtiene solo la columna 'email'
                                ->toArray();
        }
        $this->resetFormAndSubModals(); // Resetea formularios internos
        $this->showCampaignInterfaceModal = true; // Muestra el modal
    }

    // Cierra el modal principal.
    public function closeInterfaceModal()
    {
        $this->showCampaignInterfaceModal = false;
        $this->closeCreateEditFormModal(); // También cierra el modal de creación/edición si estuviera abierto
    }

    // Abre el modal interno para crear o editar una campaña.
    public function openCreateEditFormModal()
    {
        $this->resetForm(); // Limpia el formulario
        $this->editingCampaignId = null; // Asegura que no estamos en modo edición
        $this->campaignToEdit = null;
        $this->showCreateEditFormModal = true; // Muestra el modal
    }

    // Cierra el modal interno de creación/edición.
    public function closeCreateEditFormModal()
    {
        $this->showCreateEditFormModal = false;
        // No se resetea el formulario aquí para permitir al usuario cerrar y reabrir
        // el modal sin perder los datos que estaba ingresando.
    }

    // Helper para resetear el formulario principal y cerrar sub-modales.
    private function resetFormAndSubModals()
    {
        $this->resetForm();
        $this->showCreateEditFormModal = false;
    }

    // Resetea todas las propiedades del formulario a sus valores iniciales.
    public function resetForm()
    {
        $this->resetValidation(); // Limpia errores de validación de Livewire
        $this->campaign_name = '';
        $this->offer_id = null;
        $this->email_subject = '';
        $this->email_body_html = '';
        $this->attachments = [];
        $this->recipient_source_users = [];
        $this->recipient_source_guests = [];
        $this->manual_emails_text = '';
        $this->parsed_manual_emails = [];
        $this->final_recipient_list = [];
        $this->editingCampaignId = null;
        $this->campaignToEdit = null;
        $this->date_of_send = Carbon::now()->addDay()->startOfHour()->format('Y-m-d\TH:i');
        // Despacha un evento para que el editor Trix limpie su contenido.
        // 'email_body_html_input_new_campaign' debe ser el ID del input Trix en la vista.
        $this->dispatch('trix-clear', 'email_body_html_input_new_campaign');
    }

    // Hook de Livewire: se ejecuta cuando la propiedad $attachments se actualiza (ej. al subir un archivo).
    public function updatedAttachments() { $this->validateOnly('attachments.*'); } // Valida solo los adjuntos

    // Elimina un archivo adjunto de la lista temporal.
    public function removeAttachment($index) {
        if (isset($this->attachments[$index])) {
            array_splice($this->attachments, $index, 1); // Elimina el elemento del array
        }
    }

    // Procesa el campo de texto `manual_emails_text` para extraer emails válidos.
    public function parseManualEmails()
    {
        $this->parsed_manual_emails = [];
        if (!empty($this->manual_emails_text)) {
            // Divide el string por espacios, comas o punto y coma.
            $emails = preg_split('/[\s,;]+/', $this->manual_emails_text);
            foreach ($emails as $email) {
                $trimmedEmail = trim($email);
                // Valida cada email y lo añade si es correcto.
                if (filter_var($trimmedEmail, FILTER_VALIDATE_EMAIL)) {
                    $this->parsed_manual_emails[] = $trimmedEmail;
                }
            }
            $this->parsed_manual_emails = array_unique($this->parsed_manual_emails); // Elimina duplicados
        }
    }

    // Compila la lista final de destinatarios a partir de todas las fuentes (usuarios, invitados, manuales).
    public function compileFinalRecipientList(): bool
    {
        $this->parseManualEmails(); // Asegura que los emails manuales estén procesados
        $recipients = collect(); // Usa una colección de Laravel para facilitar la manipulación

        // Añade emails de usuarios registrados seleccionados
        if (!empty($this->recipient_source_users)) {
            $selectedUserEmails = User::whereIn('id', $this->recipient_source_users)->pluck('email');
            $recipients = $recipients->merge($selectedUserEmails);
        }
        // Añade emails de invitados seleccionados
        if (!empty($this->recipient_source_guests)) {
            $recipients = $recipients->merge($this->recipient_source_guests);
        }
        // Añade emails parseados manualmente
        if (!empty($this->parsed_manual_emails)) {
            $recipients = $recipients->merge($this->parsed_manual_emails);
        }

        // Genera la lista final: única, sin nulos/vacíos, y reindexada.
        $this->final_recipient_list = $recipients->unique()->filter()->values()->toArray();

        // Valida que haya al menos un destinatario.
        if (empty($this->final_recipient_list)) {
             $this->addError('final_recipient_list', 'Debes seleccionar o añadir al menos un destinatario.');
             return false; // Indica fallo
        }
        $this->resetErrorBag('final_recipient_list'); // Limpia error si todo está bien
        return true; // Indica éxito
    }

    // Guarda la campaña (crea una nueva o actualiza una existente).
    public function saveCampaign()
    {
        $validatedData = $this->validate(); // Valida todos los campos del formulario

        // Compila y valida la lista de destinatarios. Si falla, muestra error y detiene.
        if (!$this->compileFinalRecipientList()) {
            $this->setNotification('No se han especificado destinatarios válidos para la campaña.', 'error');
            return;
        }

        $attachmentPaths = []; // Array para guardar las rutas de los adjuntos almacenados
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $key => $attachment) {
                // Comprobación de seguridad: asegura que el adjunto es un objeto y tiene métodos esperados.
                 if(!is_object($attachment) || !method_exists($attachment, 'getClientOriginalExtension')) {
                    Log::warning('Elemento inesperado en $attachments al guardar campaña:', ['key' => $key, 'type' => gettype($attachment)]);
                    continue; // Salta este adjunto si no es válido
                }
                // Define una carpeta única para los adjuntos de esta campaña.
                $campaignFolder = 'campaign_attachments/' . ($this->editingCampaignId ?? Str::slug($this->campaign_name) . '_' . time());
                // Genera un nombre de archivo único y sanitizado.
                $filename = Str::random(10) . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $attachment->getClientOriginalName());
                // Almacena el archivo en el disco 'public' y obtiene su ruta.
                $path = $attachment->storeAs($campaignFolder, $filename, 'public');
                $attachmentPaths[] = $path;
            }
        }

        // Prepara los datos para crear/actualizar el modelo EmailCampaign.
        $data = [
            'campaign_name' => $validatedData['campaign_name'],
            'offer_id' => $validatedData['offer_id'] ?? null, // Asigna null si no se seleccionó oferta
            'email_subject' => $validatedData['email_subject'],
            'email_body_html' => $validatedData['email_body_html'],
            'date_of_send' => Carbon::parse($validatedData['date_of_send']), // Convierte la fecha a objeto Carbon
            'status' => 'pending', // Estado inicial de la campaña
            'recipients_snapshot' => $this->final_recipient_list, // Guarda la lista de destinatarios
        ];

        if ($this->editingCampaignId) { // Si estamos editando una campaña existente
            $campaign = EmailCampaign::find($this->editingCampaignId);
            if (!$campaign) {
                $this->setNotification('Error: No se encontró la campaña para actualizar.', 'error');
                $this->closeCreateEditFormModal();
                return;
            }
            // Si se subieron nuevos adjuntos, elimina los antiguos y usa los nuevos.
            if (!empty($attachmentPaths)) {
                if ($campaign->attachment_paths) { // Si había adjuntos previos
                    foreach ($campaign->attachment_paths as $oldPath) { Storage::disk('public')->delete($oldPath); }
                }
                $data['attachment_paths'] = $attachmentPaths;
            } else {
                 // Si no se subieron nuevos, mantiene los adjuntos existentes (si los hay).
                 $data['attachment_paths'] = $campaign->attachment_paths;
            }
            $campaign->update($data);
            $this->setNotification('Campaña actualizada con éxito.', 'success');
        } else { // Si es una nueva campaña
            $data['attachment_paths'] = !empty($attachmentPaths) ? $attachmentPaths : null;
            EmailCampaign::create($data);
            $this->setNotification('Campaña creada y programada con éxito.', 'success');
        }

        $this->showCreateEditFormModal = false; // Cierra el modal de creación/edición
        $this->resetForm(); // Limpia el formulario para la próxima vez
    }

    // Prepara el formulario para editar una campaña existente.
    public function editCampaign(EmailCampaign $campaign)
    {
        $this->resetForm(); // Limpia cualquier dato previo
        $this->editingCampaignId = $campaign->id; // Establece el ID de la campaña a editar
        $this->campaignToEdit = $campaign; // Guarda la instancia del modelo

        // Carga los datos de la campaña en las propiedades del formulario.
        $this->campaign_name = $campaign->campaign_name;
        $this->offer_id = $campaign->offer_id;
        $this->email_subject = $campaign->email_subject;
        $this->email_body_html = $campaign->email_body_html;
        // Despacha un evento para que el editor Trix actualice su contenido.
        $this->dispatch('trix-input', ['id' => 'email_body_html_input_new_campaign', 'content' => $this->email_body_html]);
        $this->date_of_send = Carbon::parse($campaign->date_of_send)->format('Y-m-d\TH:i'); // Formatea la fecha para el input datetime-local
        $this->attachments = []; // Los adjuntos existentes no se precargan para subida, se manejan en saveCampaign.

        // Nota: La recarga de destinatarios para edición es compleja y se omite por simplicidad.
        // Se podría implementar lógica para rellenar los selectores de destinatarios
        // basándose en `recipients_snapshot`, pero puede ser confuso.
        // Actualmente, al editar, los destinatarios se deben re-seleccionar o se asume
        // que se mantienen los de `recipients_snapshot` si no se modifican explícitamente.

        $this->showCreateEditFormModal = true; // Muestra el modal de edición
    }

    // Elimina una campaña.
    public function deleteCampaign(EmailCampaign $campaign)
    {
        try {
            // Si la campaña tiene adjuntos, los elimina del almacenamiento.
            if ($campaign->attachment_paths) {
                foreach ($campaign->attachment_paths as $path) { Storage::disk('public')->delete($path); }
            }
            $campaign->delete(); // Elimina el registro de la base de datos
            $this->setNotification('Campaña eliminada con éxito.', 'success');
        } catch (\Exception $e) {
            Log::error("Error al eliminar campaña {$campaign->id}: " . $e->getMessage());
            $this->setNotification('No se pudo eliminar la campaña.', 'error');
        }
    }

    // Resetea el mensaje de notificación.
    private function resetNotification()
    {
        $this->notificationMessage = '';
        $this->notificationType = '';
    }

    // Envía una campaña inmediatamente (si está pendiente o fallida).
    public function sendCampaignNow(EmailCampaign $campaign)
    {
        $this->resetNotification(); // Limpia notificaciones previas
        // Verifica que la campaña pueda ser enviada.
        if ($campaign->status !== 'pending' && $campaign->status !== 'failed') {
            $this->setNotification("La campaña ID {$campaign->id} no está pendiente o fallida (estado: {$campaign->status}).", 'info');
            return;
        }

        $recipients = $campaign->recipients_snapshot ?? []; // Obtiene la lista de destinatarios guardada

        // Verifica que haya destinatarios.
        if (empty($recipients)) {
            $this->setNotification("No se encontraron destinatarios para la campaña ID {$campaign->id}. Asegúrate de que la campaña tiene destinatarios guardados.", 'error');
            $campaign->update(['status' => 'failed', 'date_of_send' => Carbon::now()]); // Marca como fallida
            return;
        }

        // Intenta aumentar el tiempo máximo de ejecución del script, útil para envíos largos.
        try {
            set_time_limit(count($recipients) * 5 + 30); // 5 segundos por destinatario + 30s de margen
        } catch(\Exception $e) {
            Log::warning("No se pudo cambiar set_time_limit: " . $e->getMessage());
        }

        $sentCount = 0; // Contador de emails enviados
        $failedEmailsLog = []; // Log de emails que fallaron

        Log::info("Iniciando envío SÍNCRONO para campaña ID {$campaign->id} a " . count($recipients) . " destinatarios.");

        // Itera sobre cada destinatario para enviar el email.
        foreach ($recipients as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Valida el formato del email
                Log::warning("Email inválido omitido en campaña {$campaign->id}: {$email}");
                $failedEmailsLog[] = $email . " (formato inválido)";
                continue; // Salta este email
            }
            try {
                // Envía el email usando la fachada Mail de Laravel.
                Mail::html($campaign->email_body_html, function ($message) use ($email, $campaign) {
                    $message->to($email) // Destinatario
                            ->subject($campaign->email_subject) // Asunto
                            ->from(config('mail.from.address'), config('mail.from.name')); // Remitente (desde config/mail.php)
                    // Adjunta archivos si existen.
                    if ($campaign->attachment_paths && is_array($campaign->attachment_paths)) {
                        foreach ($campaign->attachment_paths as $path) {
                            if (Storage::disk('public')->exists($path)) {
                                $message->attach(Storage::disk('public')->path($path)); // Adjunta el archivo
                            } else {
                                Log::warning("Adjunto no encontrado para campaña {$campaign->id} al enviar a {$email}: {$path}");
                            }
                        }
                    }
                });
                $sentCount++;
                // Pequeña pausa cada 30 correos si la lista es grande, para no sobrecargar el servidor de correo.
                if ($sentCount % 30 == 0 && count($recipients) > 50) {
                    sleep(1);
                }
            } catch (\Exception $e) {
                Log::error("Error enviando email de campaña {$campaign->id} a {$email}: " . $e->getMessage());
                $failedEmailsLog[] = $email; // Añade el email fallido al log
            }
        }

        $finalStatus = 'sent'; // Estado por defecto después del envío
        $notificationText = "Campaña ID {$campaign->id} procesada. {$sentCount} correos intentados.";

        // Si hubo fallos, actualiza el estado y el mensaje.
        if (!empty($failedEmailsLog)) {
            $finalStatus = 'failed'; // O 'partial_failure' si prefieres un estado intermedio
            $notificationText .= " " . count($failedEmailsLog) . " envíos fallaron. Revisa los logs.";
            Log::error("Lista de emails fallidos para campaña {$campaign->id}: " . implode(', ', $failedEmailsLog));
        }

        // Actualiza el estado de la campaña y la fecha de envío (a la actual).
        $campaign->update([
            'status' => $finalStatus,
            'date_of_send' => Carbon::now()
        ]);
        $this->setNotification($notificationText, empty($failedEmailsLog) ? 'success' : 'error');
    }

    // Establece un mensaje de notificación para mostrar al usuario.
    private function setNotification(string $message, string $type)
    {
        $this->notificationMessage = $message;
        $this->notificationType = $type;
        // Despacha un evento para que la vista (Alpine.js) pueda mostrar la notificación.
        $this->dispatch('notification-message');
    }

    // Hooks de Livewire: se ejecutan cuando las propiedades de búsqueda se actualizan.
    // Resetean la paginación para las listas correspondientes.
    public function updatingSearchTermUsers() { $this->resetPage('usersPage'); }
    public function updatingSearchTermGuests() { $this->resetPage('guestsPage'); }

    // Método `render`: se encarga de obtener los datos necesarios y devolver la vista Blade.
    public function render()
    {
        // Establece el idioma para Carbon, para formatos de fecha localizados.
        Carbon::setLocale(config('app.locale', 'es'));

        $campaignsPaginated = collect(); // Inicializa como colección vacía
        $availableUsers = $this->users ?? collect(); // Usa usuarios ya cargados si existen
        $availableGuestEmails = collect($this->guest_emails ?? []); // Usa emails de invitados ya cargados

        // Si el modal principal está abierto, carga las campañas paginadas.
        if ($this->showCampaignInterfaceModal) {
            $campaignsPaginated = EmailCampaign::with('offer') // Carga la relación 'offer' para mostrar nombre de oferta
                ->orderBy('date_of_send', 'desc') // Ordena por fecha de envío descendente
                ->paginate(5, ['*'], 'campaignsPage'); // Pagina los resultados

            // Si el modal de creación/edición también está abierto, carga/filtra listas de destinatarios.
            if ($this->showCreateEditFormModal) {
                 // Carga usuarios si no se han cargado antes o si hay término de búsqueda.
                 if($this->users === null || !empty($this->searchTermUsers)) {
                     $availableUsers = User::where('role', 'user') // Solo usuarios con rol 'user'
                        ->when($this->searchTermUsers, function ($query) { // Aplica filtro de búsqueda si existe
                            $query->where(function ($q) {
                                $q->where('name', 'like', '%'.$this->searchTermUsers.'%')
                                  ->orWhere('email', 'like', '%'.$this->searchTermUsers.'%');
                            });
                        })
                        ->orderBy('name')->limit(50)->get(['id', 'name', 'email']);
                     if(empty($this->searchTermUsers)) $this->users = $availableUsers; // Guarda si no es búsqueda para evitar recargas
                 } else {
                    $availableUsers = $this->users; // Usa los ya cargados
                 }

                // Carga emails de invitados si no se han cargado antes o si hay término de búsqueda.
                if($this->guest_emails === null || !empty($this->searchTermGuests)) {
                    $availableGuestEmails = collect(Superappointment::whereNotNull('email')
                        ->when($this->searchTermGuests, function ($query) { // Aplica filtro de búsqueda
                             $query->where(function ($q) {
                                $q->where('email', 'like', '%'.$this->searchTermGuests.'%')
                                  ->orWhere('guest_name', 'like', '%'.$this->searchTermGuests.'%'); // También busca por nombre de invitado
                             });
                        })
                        ->distinct()->orderBy('email')->limit(50)->pluck('email')->toArray());
                    if(empty($this->searchTermGuests)) $this->guest_emails = $availableGuestEmails->all(); // Guarda si no es búsqueda
                } else {
                    $availableGuestEmails = collect($this->guest_emails); // Usa los ya cargados
                }
            }
        }

        // Retorna la vista Blade y le pasa los datos necesarios.
        return view('livewire.admin.email-campaign-creator', [
            'campaigns' => $campaignsPaginated,
            'availableUsers' => $availableUsers,
            'availableGuestEmails' => $availableGuestEmails,
        ]);
    }
}
