<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
      public function up(): void {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->json('recipients_snapshot')->nullable()->after('attachment_paths')->comment('JSON array of email addresses for this campaign');
        });
    }
    public function down(): void {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropColumn('recipients_snapshot');
        });
    }
};
