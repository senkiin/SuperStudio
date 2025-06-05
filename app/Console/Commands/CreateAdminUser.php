<?php

namespace App\Console\Commands;

use App\Models\User; // Asegúrate que el namespace de tu modelo User sea correcto
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// Si no usas Str::password, puedes quitar Illuminate\Support\Str
// pero es bueno tenerlo por si acaso para futuras mejoras.

class CreateAdminUser extends Command
{
    // Firma del comando: app:create-admin
    // {name?} : Argumento opcional para el nombre
    // {email?} : Argumento opcional para el email
    // {password?} : Argumento opcional para la contraseña
    protected $signature = 'app:create-admin {name?} {email?} {password?}';

    protected $description = 'Crea un nuevo usuario administrador';

    public function handle(): int
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        // Si no se pasan todos los argumentos, pedirlos interactivamente
        if (!$name || !$email || !$password) {
            $this->info('Se requieren nombre, email y contraseña. Proporcionándolos como argumentos:');
            $this->line('php artisan app:create-admin "Tu Nombre" "tuemail@example.com" "tucontraseña"');
            // Si quieres permitir interactividad (puede no funcionar en todos los paneles de comandos):
            // $name = $name ?: $this->ask('Nombre del administrador:');
            // $email = $email ?: $this->ask('Email del administrador:');
            // $password = $password ?: $this->secret('Contraseña para el administrador:');
            return Command::FAILURE; // Fallar si no se proporcionan todos los argumentos en un entorno no interactivo
        }

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // Valida unicidad
            'password' => ['required', 'string', 'min:8'], // Mínimo 8 caracteres
        ]);

        if ($validator->fails()) {
            $this->error('Error de validación:');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'email_verified_at' => now(), // Marcar el email como verificado
                'password' => Hash::make($password),
                'role' => 'admin', // Asegúrate que 'admin' es el valor correcto
            ]);

            $this->info("¡Usuario administrador '{$user->name}' creado con éxito!");
            $this->line("Email: {$user->email}");
            // No mostraremos la contraseña.

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('No se pudo crear el usuario administrador: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
