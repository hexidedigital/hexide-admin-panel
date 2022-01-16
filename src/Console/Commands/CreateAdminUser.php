<?php

namespace HexideDigital\HexideAdmin\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class CreateAdminUser extends Command
{
    protected $name = 'hd-admin:create:admin-user';
    protected $description = 'Create new user with admin roles';
    private ?string $showPassword = null;

    public function handle(): int
    {
        try {
            $email = $this->getEmail();

            $name = $this->option('name') ?: $this->ask('Enter name', \Arr::first(explode('@', $email)));
            $name = $name ?: \Arr::first(explode('@', $email));

            $password = $this->getHashedPassword();

            $is_system = false;
            $role = Role::Admin;

            if (in_array('is_system', (new User)->getHidden()) && $email === 'super-admin@admin.com') {
                $is_system = true;
                $role = Role::SuperAdmin;
            }

            $user = User::create(compact('name', 'email', 'name', 'is_system', 'password'));

            $user->roles()->attach($role);

            $this->info('New user for admin panel created');
            $this->table(['field', 'value'], [
                ['name', $name],
                ['email', $email],
                ['password', $this->showPassword ?: '********'],
                ['url', route('admin.login')],
            ]);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    protected function getEmail()
    {
        $emailExists = true;
        $times = 3;

        $email = $this->option('email');

        while ($emailExists && $times-- >= 0) {
            $random = str_slug(\Faker\Factory::create()->userName) . '@admin.com';
            $email = $email ?: $this->ask('Enter email', $random);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = null;
                $this->warn('Email invalid.');
                continue;
            }

            $emailExists = User::where('email', $email)->count() > 0;

            if ($emailExists) {
                $email = null;
                $this->warn('Email exists, enter another.');
                continue;
            }

            $emailExists = false;
        }

        if ($emailExists) {
            throw new \Exception('Email invalid or existed in database.');
        }

        return $email;
    }

    protected function getHashedPassword(): string
    {
        $hash = $this->option('hash');

        if ($hash) {
            return $hash;
        }

        if ($this->option('generate')) {
            return bcrypt($this->generatePassword());
        }

        $invalid = true;
        $times = 3;
        $password = $this->option('password');

        while ($invalid && $times-- >= 0) {
            $password = $password ?: $this->secret('Enter password (length 4-20 chars, but if empty will be autogenerated)');

            if (empty($password)) {
                $invalid = false;
                $password = $this->generatePassword();
                continue;
            }

            if (!$this->isCorrectLength($password)) {
                $password = null;
                $this->warn('Invalid password length');
                continue;
            }

            if ($password !== $this->secret('Confirm password')) {
                $password = null;
                $this->warn('Invalid confirm password');
                continue;
            }

            $invalid = false;
        }

        if ($invalid) {
            throw new \Exception('Invalid password');
        }

        return bcrypt($password);
    }

    protected function generatePassword(): string
    {
        return $this->showPassword = \Str::random(12);
    }

    protected function isCorrectLength(string $password): bool
    {
        return 4 <= strlen($password) && strlen($password) <= 20;
    }

    protected function getOptions(): array
    {
        return [
            new InputOption('email', 'e', InputOption::VALUE_OPTIONAL, 'Set email'),
            new InputOption('name', null, InputOption::VALUE_OPTIONAL, 'Set name'),
            new InputOption('password', 'p', InputOption::VALUE_OPTIONAL, 'Set password (Is not secure)'),
            new InputOption('hash', null, InputOption::VALUE_OPTIONAL, 'Set password hash, maybe from tinker (more secure)'),
            new InputOption('generate', 'g', InputOption::VALUE_NONE, 'Autogenerate password'),
        ];
    }
}
