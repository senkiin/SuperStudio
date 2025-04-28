<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

  class Kernel extends ConsoleKernel
{
/**
* Define the application's command schedule.
*/
protected function schedule(Schedule $schedule): void
{
// $schedule->command('inspire')->hourly(); // Ejemplo que suele venir por defecto  

        // ----- TU TAREA PROGRAMADA -----
        // Llama al comando 'app:fetch-google-reviews'.
        // Puedes elegir la frecuencia que más te convenga:

        // Opción 1: Ejecutar todos los días a las 3:00 AM
        $schedule->command('app:fetch-google-reviews')->dailyAt('03:00');

        // Opción 2: Ejecutar cada 6 horas
        // $schedule->command('app:fetch-google-reviews')->cron('0 */6 * * *');

        // Opción 3: Ejecutar cada día al mediodía
        // $schedule->command('app:fetch-google-reviews')->dailyAt('12:00');

        // Opción 4: Ejecutar cada hora
        // $schedule->command('app:fetch-google-reviews')->hourly();

        // Opción 5: Ejecutar cada 15 minutos (quizás excesivo para reseñas)
        // $schedule->command('app:fetch-google-reviews')->everyFifteenMinutes();

        // ----- FIN DE TU TAREA -----
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
