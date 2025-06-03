<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Make user_id nullable and set onDelete to set null
            
            // Add new columns for guest details
            $table->string('guest_name')->nullable()->after('user_id');
            $table->string('email')->after('guest_name'); // Assuming email is always required
            $table->string('phone')->nullable()->after('email');

            // Change appointment_date to appointment_datetime
            $table->renameColumn('appointment_date', 'appointment_datetime_old'); // Rename temporarily
        });

        Schema::table('appointments', function (Blueprint $table) {
            // Add the new datetime column
            $table->dateTime('appointment_datetime')->after('notes'); // Or your preferred position

            // If you have existing data in 'appointment_datetime_old' (formerly 'appointment_date'),
            // you might want to copy it. Laravel's schema builder doesn't directly support
            // copying data during a type change like this. You might need a separate DB::update or raw SQL.
            // For simplicity, this example assumes new data or manual data migration.
            // If `appointment_datetime_old` stored time, you'd need to combine it.
            // If it was just a date, it will become YYYY-MM-DD 00:00:00
            // DB::statement('UPDATE appointments SET appointment_datetime = appointment_datetime_old'); // Example for SQLite or simple date copy

            // Drop the old date column
            $table->dropColumn('appointment_datetime_old');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Revert appointment_datetime to appointment_date
            $table->renameColumn('appointment_datetime', 'appointment_datetime_temp_revert');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->date('appointment_date')->after('notes'); // Add back as date

            // Potentially copy data back if needed, though data loss (time part) is likely
            // DB::statement('UPDATE appointments SET appointment_date = DATE(appointment_datetime_temp_revert)');

            $table->dropColumn('appointment_datetime_temp_revert');

            // Revert guest_name, email, phone columns
            $table->dropColumn(['guest_name', 'email', 'phone']);

            // Revert user_id to not nullable and onDelete cascade (original state)
            // This might fail if there are NULLs or if dependent records prevent onDelete('cascade')
            if (DB::getDriverName() !== 'sqlite') {
                 $table->dropForeign(['user_id']);
            }
            // Important: Changing back to not nullable requires no existing null values.
            // You might need to handle this case (e.g., set a default user_id or delete rows with null user_id)
            // For simplicity, we assume this is handled or the table is empty during rollback.
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->change();
        });
    }
};
