<?php

namespace HexideDigital\HexideAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SetupProjectCommand extends Command
{
    private string $projectName;
    private array $database = [];

    protected $name = 'hd-admin:create:env';
    protected $description = 'Create .env file for project';

    public function handle(): int
    {
        try {
            $this->setProjectName();

            if ($this->option('database')) {
                $this->createUserAndDatabase();
            }

            $this->createEnv();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } finally {
            return self::SUCCESS;
        }
    }

    /** @throws \Exception */
    private function setProjectName()
    {
        $project = $this->argument('project') ?? $this->ask('Project projectName');

        if (empty($project)) {
            throw new \Exception('Incorrect arguments');
        }

        $names = explode('.', $project);
        if (1 < sizeof($names)) {
            $names = array_slice($names, 1);
        }

        $this->projectName = Str::slug(strtolower(implode('-', $names)));
    }

    /** @throws \Exception */
    private function createEnv()
    {
        $this->info('Changing .env parameters');

        if (file_exists($old_env = base_path('/.env'))) {
            copy($old_env, base_path('/.env.old'));
        }

        $envFilePath = base_path('/.env');
        copy(base_path('/.env.example'), $envFilePath);

        $this->replaceInFile([
            'APP_NAME=project_name'        => 'APP_NAME="' . $this->projectName . '"',
            'DB_DATABASE=laravel_database' => 'DB_DATABASE=' . Arr::get($this->database, 'name', 'laravel_database'),
            'DB_USERNAME=laravel_database' => 'DB_USERNAME=' . Arr::get($this->database, 'user', 'laravel_database'),
            'DB_PASSWORD=laravel_password' => 'DB_PASSWORD=' . Arr::get($this->database, 'pass', 'laravel_password'),
        ], $envFilePath);

        $this->info('generating app_key');
        Artisan::call('key:generate', [], $this->output);

        $this->info('generating storage_link');
        Artisan::call('storage:link', [], $this->output);

        $this->info('generating jwt_secret');
        Artisan::call('jwt:secret -f', [], $this->output);

        copy($envFilePath, $envFilePath . '.' . $this->projectName);
    }

    /** @throws \Exception */
    private function createUserAndDatabase()
    {
        $rootPassword = $this->argument('password') ?? $this->secret('Database root password', '');

        $result = exec("mysql --user=root --password=$rootPassword --execute=\"exit\"");

        if (Str::contains($result, 'Access denied')) {
            $this->warn($result);

            throw new \Exception('Invalid database root password');
        }

        $dbName = 'laravel_' . $this->projectName;
        $dbPass = Str::random(10);

        $this->info('Preparing database...');

        $this->warn(exec("mysql --user=root --password=$rootPassword --execute=\"CREATE DATABASE \`$dbName\`; CREATE USER \`$dbName\`@'localhost' IDENTIFIED BY '$dbPass'; GRANT ALL PRIVILEGES ON \`$dbName\`.* TO \`$dbName\`@'localhost' WITH GRANT OPTION; FLUSH PRIVILEGES;\" "));

        $this->database = [
            'name' => $dbName,
            'user' => $dbName,
            'pass' => $dbPass,
        ];

        $this->info('User and Database are created.');
    }

    protected function replaceInFile(array $replacements, string $path)
    {
        $content = file_get_contents($path);
        $new_content = str_replace(array_keys($replacements), array_values($replacements), $content);

        file_put_contents($path, $new_content);
    }

    protected function getArguments(): array
    {
        return [
            new InputArgument('project', InputArgument::OPTIONAL, 'project name'),
            new InputArgument('password', InputArgument::OPTIONAL, 'root password'),
        ];
    }

    protected function getOptions(): array
    {
        return [
            new InputOption('database', 'd', InputOption::VALUE_OPTIONAL, 'Create user and database for project'),
        ];
    }
}
