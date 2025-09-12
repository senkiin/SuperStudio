<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User; // Make sure the path to your User model is correct.

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a user role to admin by their email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 1. Get the email from the command argument.
        $email = $this->argument('email');

        // 2. Find the user in the database.
        $user = User::where('email', $email)->first();

        // 3. Check if the user exists.
        if (!$user) {
            $this->error("Error: User with email '{$email}' not found.");
            return 1; // Return an error code.
        }

        // 4. Update the 'role' column to 'admin'.
        //    This matches your migration file.
        $user->role = 'admin';
        $user->save();

        // 5. Display a success message in the console.
        $this->info("Success! The user {$user->name} ({$email}) now has the 'admin' role.");

        return 0; // Return a success code.
    }
}
