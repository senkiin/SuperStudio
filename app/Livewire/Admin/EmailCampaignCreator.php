<?php

namespace App\Livewire\Admin;

use App\Models\EmailCampaign;
use App\Models\Offer;
use App\Models\User;
use App\Models\Superappointment;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema; // Importar la fachada Schema

class EmailCampaignCreator extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Propiedad para controlar la visibilidad del modal principal del componente
    public $showCampaignInterfaceModal = false;

    // Propiedades del formulario de la campaña (usado en el modal interno de creación/edición)
    public string $campaign_name = '';
    public $offer_id = null;
    public string $email_subject = '';
    public string $email_body_html = ''; // Se enlazará con el editor Trix
    public $date_of_send;
    public $attachments = []; // Para los archivos subidos temporalmente

    // Gestión de destinatarios
    public array $recipient_source_users = []; // IDs de usuarios seleccionados
    public array $recipient_source_guests = []; // Emails de invitados seleccionados
    public string $manual_emails_text = ''; // Emails añadidos manualmente como texto
    public array $parsed_manual_emails = [];
    public array $final_recipient_list = []; // Lista consolidada de correos únicos

    // Datos para selectores y listas
    public $offers = [];
    // Estas se cargan dinámicamente o cuando se abre el modal principal
    public $users;
    public $guest_emails;

    // Edición y estado
    public $editingCampaignId = null;
    public $campaignToEdit;

    // UI
    public $showCreateEditFormModal = false; // Para el modal interno de creación/edición
    public string $searchTermUsers = '';
    public string $searchTermGuests = '';

    public string $notificationMessage = '';
    public string $notificationType = ''; // 'success', 'error', 'info'
    // Listener para el evento desde el dashboard
    protected $listeners = ['openEmailCampaignModalEvent' => 'openInterfaceModal'];

    protected function rules()
    {
        return [
            'campaign_name' => 'required|string|max:255',
            'offer_id' => 'nullable|sometimes|exists:offers,id', // 'sometimes' para que valide solo si está presente
            'email_subject' => 'required|string|max:255',
            'email_body_html' => 'required|string|min:50',
            'date_of_send' => 'required|date|after_or_equal:' . Carbon::today()->toDateString(),
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:5120', // Max 5MB por archivo, tipos específicos
            'recipient_source_users' => 'nullable|array',
            'recipient_source_guests' => 'nullable|array',
            'manual_emails_text' => 'nullable|string',
        ];
    }

    protected function messages() {
        return [
            'campaign_name.required' => 'El nombre de la campaña es obligatorio.',
            // 'offer_id.required' => 'Debes seleccionar una oferta para la campaña.', // Comentado porque ahora es opcional
            'email_subject.required' => 'El asunto del email es obligatorio.',
            'email_body_html.required' => 'El contenido del email es obligatorio.',
            'email_body_html.min' => 'El contenido del email debe tener al menos 50 caracteres.',
            'date_of_send.required' => 'La fecha de envío es obligatoria.',
            'date_of_send.after_or_equal' => 'La fecha de envío debe ser hoy o una fecha futura.',
            'attachments.*.mimes' => 'El archivo adjunto debe ser de tipo: pdf, doc, docx, jpg, jpeg, png, gif.',
            'attachments.*.max' => 'Cada archivo adjunto no debe superar los 5MB.',
        ];
    }

    public function mount()
    {
        try {
            if (Schema::hasTable('offers')) {
                $query = Offer::query();
                if (Schema::hasColumn('offers', 'name')) {
                    $query->orderBy('name');
                    $this->offers = $query->get(['id', 'name']);
                } elseif (Schema::hasColumn('offers', 'title')) {
                    $query->orderBy('title');
                    $this->offers = $query->select('id', 'title as name')->get();
                } elseif (Schema::hasColumn('offers', 'id')) {
                    $this->offers = $query->get(['id']); // Solo IDs si no hay campo de nombre
                     Log::warning("Tabla 'offers' no tiene columna 'name' o 'title'. Cargando solo IDs para ofertas.");
                } else {
                     $this->offers = collect(); // No hay columnas usables
                     Log::warning("Tabla 'offers' no tiene columnas 'id', 'name', o 'title'. Las ofertas no se cargarán.");
                }
            } else {
                 $this->offers = collect();
                 Log::info("La tabla 'offers' no existe. Las ofertas no se cargarán para campañas.");
            }
        } catch (\Exception $e) {
            Log::error("Error crítico al cargar ofertas en EmailCampaignCreator: " . $e->getMessage());
            $this->offers = collect();
        }
        $this->date_of_send = Carbon::now()->addDay()->startOfHour()->format('Y-m-d\TH:i');
    }

    public function openInterfaceModal()
    {
        if ($this->users === null) {
             $this->users = User::where('role', 'user')->orderBy('name')->limit(100)->get(['id', 'name', 'email']);
        }
        if ($this->guest_emails === null) {
            $this->guest_emails = Superappointment::whereNotNull('email')
                                ->distinct()
                                ->orderBy('email')
                                ->limit(100)
                                ->pluck('email')
                                ->toArray();
        }
        $this->resetFormAndSubModals();
        $this->showCampaignInterfaceModal = true;
    }

    public function closeInterfaceModal()
    {
        $this->showCampaignInterfaceModal = false;
        $this->closeCreateEditFormModal();
    }

    public function openCreateEditFormModal()
    {
        $this->resetForm();
        $this->editingCampaignId = null;
        $this->campaignToEdit = null;
        $this->showCreateEditFormModal = true;
    }

    public function closeCreateEditFormModal()
    {
        $this->showCreateEditFormModal = false;
        // No resetear el formulario aquí para permitir al usuario cerrar y reabrir sin perder datos
        // El reseteo se hace en openCreateEditFormModal o después de guardar
    }

    private function resetFormAndSubModals()
    {
        $this->resetForm();
        $this->showCreateEditFormModal = false;
    }

    public function resetForm()
    {
        $this->resetValidation();
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
        $this->dispatch('trix-clear', 'email_body_html_input_new_campaign');
    }

    public function updatedAttachments() { $this->validateOnly('attachments.*'); }

    public function removeAttachment($index) {
        if (isset($this->attachments[$index])) {
            array_splice($this->attachments, $index, 1);
        }
    }

    public function parseManualEmails()
    {
        $this->parsed_manual_emails = [];
        if (!empty($this->manual_emails_text)) {
            $emails = preg_split('/[\s,;]+/', $this->manual_emails_text);
            foreach ($emails as $email) {
                $trimmedEmail = trim($email);
                if (filter_var($trimmedEmail, FILTER_VALIDATE_EMAIL)) {
                    $this->parsed_manual_emails[] = $trimmedEmail;
                }
            }
            $this->parsed_manual_emails = array_unique($this->parsed_manual_emails);
        }
    }

    public function compileFinalRecipientList(): bool
    {
        $this->parseManualEmails();
        $recipients = collect();

        if (!empty($this->recipient_source_users)) {
            $selectedUserEmails = User::whereIn('id', $this->recipient_source_users)->pluck('email');
            $recipients = $recipients->merge($selectedUserEmails);
        }
        if (!empty($this->recipient_source_guests)) {
            $recipients = $recipients->merge($this->recipient_source_guests);
        }
        if (!empty($this->parsed_manual_emails)) {
            $recipients = $recipients->merge($this->parsed_manual_emails);
        }

        $this->final_recipient_list = $recipients->unique()->filter()->values()->toArray();

        if (empty($this->final_recipient_list)) {
             $this->addError('final_recipient_list', 'Debes seleccionar o añadir al menos un destinatario.');
             return false;
        }
        $this->resetErrorBag('final_recipient_list');
        return true;
    }

    public function saveCampaign()
    {
        $validatedData = $this->validate();

        if (!$this->compileFinalRecipientList()) {
            $this->setNotification('No se han especificado destinatarios válidos para la campaña.', 'error');
            return;
        }

        $attachmentPaths = [];
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $key => $attachment) {
                 if(!is_object($attachment) || !method_exists($attachment, 'getClientOriginalExtension')) {
                    Log::warning('Elemento inesperado en $attachments al guardar campaña:', ['key' => $key, 'type' => gettype($attachment)]);
                    continue;
                }
                $campaignFolder = 'campaign_attachments/' . ($this->editingCampaignId ?? Str::slug($this->campaign_name) . '_' . time());
                $filename = Str::random(10) . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $attachment->getClientOriginalName());
                $path = $attachment->storeAs($campaignFolder, $filename, 'public');
                $attachmentPaths[] = $path;
            }
        }

        $data = [
            'campaign_name' => $validatedData['campaign_name'],
            'offer_id' => $validatedData['offer_id'] ?? null,
            'email_subject' => $validatedData['email_subject'],
            'email_body_html' => $validatedData['email_body_html'],
            'date_of_send' => Carbon::parse($validatedData['date_of_send']),
            'status' => 'pending',
            'recipients_snapshot' => $this->final_recipient_list, // Guardar la lista de destinatarios
        ];

        if ($this->editingCampaignId) {
            $campaign = EmailCampaign::find($this->editingCampaignId);
            if (!$campaign) {
                $this->setNotification('Error: No se encontró la campaña para actualizar.', 'error');
                $this->closeCreateEditFormModal();
                return;
            }
            if (!empty($attachmentPaths)) {
                if ($campaign->attachment_paths) {
                    foreach ($campaign->attachment_paths as $oldPath) { Storage::disk('public')->delete($oldPath); }
                }
                $data['attachment_paths'] = $attachmentPaths;
            } else {
                 $data['attachment_paths'] = $campaign->attachment_paths;
            }
            $campaign->update($data);
            $this->setNotification('Campaña actualizada con éxito.', 'success');
        } else {
            $data['attachment_paths'] = !empty($attachmentPaths) ? $attachmentPaths : null;
            EmailCampaign::create($data);
            $this->setNotification('Campaña creada y programada con éxito.', 'success');
        }

        $this->showCreateEditFormModal = false;
        $this->resetForm();
    }

    public function editCampaign(EmailCampaign $campaign)
    {
        $this->resetForm();
        $this->editingCampaignId = $campaign->id;
        $this->campaignToEdit = $campaign;

        $this->campaign_name = $campaign->campaign_name;
        $this->offer_id = $campaign->offer_id;
        $this->email_subject = $campaign->email_subject;
        $this->email_body_html = $campaign->email_body_html;
        $this->dispatch('trix-input', ['id' => 'email_body_html_input_new_campaign', 'content' => $this->email_body_html]);
        $this->date_of_send = Carbon::parse($campaign->date_of_send)->format('Y-m-d\TH:i');
        $this->attachments = [];
        // Si quieres precargar los destinatarios para edición:
        // $this->final_recipient_list = $campaign->recipients_snapshot ?? [];
        // // Podrías intentar rellenar recipient_source_users, _guests, y manual_emails_text
        // // comparando final_recipient_list con tus listas de usuarios/invitados,
        // // pero es complejo y puede ser confuso para el usuario.
        // // Por simplicidad, al editar, los destinatarios se tendrían que re-seleccionar o
        // // el sistema asumiría que se usan los 'recipients_snapshot' si no se cambian.

        $this->showCreateEditFormModal = true;
    }

    public function deleteCampaign(EmailCampaign $campaign)
    {
        try {
            if ($campaign->attachment_paths) {
                foreach ($campaign->attachment_paths as $path) { Storage::disk('public')->delete($path); }
            }
            $campaign->delete();
            $this->setNotification('Campaña eliminada con éxito.', 'success');
        } catch (\Exception $e) {
            Log::error("Error al eliminar campaña {$campaign->id}: " . $e->getMessage());
            $this->setNotification('No se pudo eliminar la campaña.', 'error');
        }
    }
private function resetNotification()
    {
        $this->notificationMessage = '';
        $this->notificationType = '';
    }
    public function sendCampaignNow(EmailCampaign $campaign)
    {
        $this->resetNotification();
        if ($campaign->status !== 'pending' && $campaign->status !== 'failed') {
            $this->setNotification("La campaña ID {$campaign->id} no está pendiente o fallida (estado: {$campaign->status}).", 'info');
            return;
        }

        $recipients = $campaign->recipients_snapshot ?? [];

        if (empty($recipients)) {
            $this->setNotification("No se encontraron destinatarios para la campaña ID {$campaign->id}. Asegúrate de que la campaña tiene destinatarios guardados.", 'error');
            $campaign->update(['status' => 'failed', 'date_of_send' => Carbon::now()]);
            return;
        }

        try {
            set_time_limit(count($recipients) * 5 + 30);
        } catch(\Exception $e) {
            Log::warning("No se pudo cambiar set_time_limit: " . $e->getMessage());
        }

        $sentCount = 0;
        $failedEmailsLog = [];

        Log::info("Iniciando envío SÍNCRONO para campaña ID {$campaign->id} a " . count($recipients) . " destinatarios.");

        foreach ($recipients as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Log::warning("Email inválido omitido en campaña {$campaign->id}: {$email}");
                $failedEmailsLog[] = $email . " (formato inválido)";
                continue;
            }
            try {
                Mail::html($campaign->email_body_html, function ($message) use ($email, $campaign) {
                    $message->to($email)
                            ->subject($campaign->email_subject)
                            ->from(config('mail.from.address'), config('mail.from.name'));
                    if ($campaign->attachment_paths && is_array($campaign->attachment_paths)) {
                        foreach ($campaign->attachment_paths as $path) {
                            if (Storage::disk('public')->exists($path)) {
                                $message->attach(Storage::disk('public')->path($path));
                            } else {
                                Log::warning("Adjunto no encontrado para campaña {$campaign->id} al enviar a {$email}: {$path}");
                            }
                        }
                    }
                });
                $sentCount++;
                if ($sentCount % 30 == 0 && count($recipients) > 50) { // Pequeña pausa cada 30 correos si son muchos
                    sleep(1);
                }
            } catch (\Exception $e) {
                Log::error("Error enviando email de campaña {$campaign->id} a {$email}: " . $e->getMessage());
                $failedEmailsLog[] = $email;
            }
        }

        $finalStatus = 'sent';
        $notificationText = "Campaña ID {$campaign->id} procesada. {$sentCount} correos intentados.";

        if (!empty($failedEmailsLog)) {
            $finalStatus = 'failed';
            $notificationText .= " " . count($failedEmailsLog) . " envíos fallaron. Revisa los logs.";
            Log::error("Lista de emails fallidos para campaña {$campaign->id}: " . implode(', ', $failedEmailsLog));
        }

        $campaign->update([
            'status' => $finalStatus,
            'date_of_send' => Carbon::now()
        ]);
        $this->setNotification($notificationText, empty($failedEmailsLog) ? 'success' : 'error');
    }

    private function setNotification(string $message, string $type)
    {
        $this->notificationMessage = $message;
        $this->notificationType = $type;
        $this->dispatch('notification-message');
    }

    public function updatingSearchTermUsers() { $this->resetPage('usersPage'); }
    public function updatingSearchTermGuests() { $this->resetPage('guestsPage'); }

    public function render()
    {
        Carbon::setLocale(config('app.locale', 'es'));

        $campaignsPaginated = collect();
        $availableUsers = $this->users ?? collect(); // Usar propiedad si ya está cargada
        $availableGuestEmails = collect($this->guest_emails ?? []); // Usar propiedad si ya está cargada

        if ($this->showCampaignInterfaceModal) {
            $campaignsPaginated = EmailCampaign::with('offer')
                ->orderBy('date_of_send', 'desc')
                ->paginate(5, ['*'], 'campaignsPage');

            if ($this->showCreateEditFormModal) {
                 if($this->users === null) { // Cargar si no se han cargado en openInterfaceModal y el submodal se abre
                     $availableUsers = User::where('role', 'user')
                        ->when($this->searchTermUsers, function ($query) {
                            $query->where(function ($q) {
                                $q->where('name', 'like', '%'.$this->searchTermUsers.'%')
                                  ->orWhere('email', 'like', '%'.$this->searchTermUsers.'%');
                            });
                        })
                        ->orderBy('name')->limit(50)->get(['id', 'name', 'email']);
                     $this->users = $availableUsers; // Guardar para no recargar innecesariamente
                 } else {
                    $availableUsers = $this->users; // Usar los ya cargados
                 }


                if($this->guest_emails === null) {
                    $availableGuestEmails = collect(Superappointment::whereNotNull('email')
                        ->when($this->searchTermGuests, function ($query) {
                             $query->where(function ($q) {
                                $q->where('email', 'like', '%'.$this->searchTermGuests.'%')
                                  ->orWhere('guest_name', 'like', '%'.$this->searchTermGuests.'%');
                             });
                        })
                        ->distinct()->orderBy('email')->limit(50)->pluck('email')->toArray());
                    $this->guest_emails = $availableGuestEmails->all(); // Guardar para no recargar
                } else {
                    $availableGuestEmails = collect($this->guest_emails); // Usar los ya cargados
                }
            }
        }

        return view('livewire.admin.email-campaign-creator', [
            'campaigns' => $campaignsPaginated,
            'availableUsers' => $availableUsers,
            'availableGuestEmails' => $availableGuestEmails,
        ]);
    }
}
