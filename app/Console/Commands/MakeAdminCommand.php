<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {email? : Email of the user to create or promote} {--name=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user, or promote an existing user to admin by email';

    public function handle(): int
    {
        $email = $this->argument('email') ?? $this->ask('Email');

        $validator = Validator::make(['email' => $email], ['email' => ['required', 'email']]);

        if ($validator->fails()) {
            $this->error('Invalid email address.');

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update(['is_admin' => true]);
            $this->info("Existing user \"{$email}\" promoted to admin.");

            return self::SUCCESS;
        }

        $name = $this->option('name') ?? $this->ask('Name');
        $password = $this->option('password') ?? $this->secret('Password');

        if (! $name || ! $password) {
            $this->error('Name and password are required to create a new admin user.');

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'client',
            'is_admin' => true,
        ]);

        $this->info("Admin user \"{$email}\" created.");

        return self::SUCCESS;
    }
}
