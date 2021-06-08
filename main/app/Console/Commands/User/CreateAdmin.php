<?php

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Class CreateAdmin.
 */
class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): ?int
    {
        $name = $this->ask('What is the name?');
        $email = $this->ask('What is the email?');
        $password = $this->getPassword();

        User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => bcrypt($password),
            'role'     => User::ROLE_ADMIN,
        ]);

        $this->info('Admin was successfully created');
    }

    private function getPassword(): string
    {
        $password = $this->secret('What is the password?');
        $passwordConfirm = $this->secret('Please, confirm password');

        if ($password !== $passwordConfirm) {
            $this->warn('Wrong password confirmation! Try again');

            return $this->getPassword();
        }

        return $password;
    }
}
