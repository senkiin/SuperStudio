<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_content_fields_to_email_campaigns_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->string('campaign_name')->after('id')->comment('Nombre interno de la campaña');
            $table->string('email_subject')->after('offer_id')->comment('Asunto del correo a enviar');
            $table->longText('email_body_html')->after('email_subject')->comment('Cuerpo HTML del correo');
            $table->json('attachment_paths')->nullable()->after('email_body_html')->comment('Rutas a los archivos adjuntos');
        });
    }

    public function down(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropColumn(['campaign_name', 'email_subject', 'email_body_html', 'attachment_paths']);
        });
    }
};
