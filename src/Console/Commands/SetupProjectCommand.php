<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;

class SetupProjectCommand extends Command
{
    protected $name = 'hd-admin:env-create';
    protected $description = 'Create .env file for project';

    private string $projectName;

    public function handle(): int
    {
        try {
            $this->setProjectName();

            $this->createEnv();

            return self::SUCCESS;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    /** @throws \InvalidArgumentException */
    private function setProjectName(): void
    {
        $dir = \File::basename(getcwd());

        $project = $this->argument('project') ?: $this->askWithCompletion('Project projectName', [$dir], $dir);

        if (empty($project)) {
            throw new \InvalidArgumentException('Incorrect projectName');
        }

        $names = explode('.', $project);

        $this->projectName = Str::slug(strtolower(implode('-', $names)));
    }

    private function createEnv(): void
    {
        $this->backupOldEnvFile();

        $this->comment('Changing .env parameters');

        $envFilePath = implode('.', [base_path('.env'), $this->projectName, date('hmi')]);
        \File::copy(base_path('.env.example'), $envFilePath);

        $this->replaceInFile([
            'APP_KEY=' => 'APP_KEY=' . $this->runAppKeyGenerate(),
            'APP_NAME=project_name' => 'APP_NAME="' . $this->projectName . '"',
            'DB_DATABASE=laravel_database' => 'DB_DATABASE=laravel_database',
            'DB_USERNAME=laravel_database' => 'DB_USERNAME=laravel_database',
            'DB_PASSWORD=laravel_password' => 'DB_PASSWORD=password',
            'DB_HOST=127.0.0.1' => 'DB_HOST=' . $this->resolveDbHost(),
        ], $envFilePath);

        \File::copy($envFilePath, base_path('.env'));

        $this->info("Env file generated [$envFilePath].");
    }

    private function backupOldEnvFile(): void
    {
        if (\File::exists($old_env = base_path('.env'))) {
            $backupName = base_path('.env.old.' . date('hmi'));
            \File::copy($old_env, $backupName);
            $this->info("Old .env file saved to path [$backupName]");
        }
    }

    private function runAppKeyGenerate(): string
    {
        $output = new BufferedOutput();
        Artisan::call('key:generate', ['--show' => true], $output);

        return trim($output->fetch());
    }

    /** @return array|bool|string */
    private function resolveDbHost()
    {
        return $this->option('db-host') ?: '127.0.0.1';
    }

    protected function replaceInFile(array $replacements, string $path): void
    {
        \File::replaceInFile(
            array_keys($replacements),
            array_values($replacements),
            $path
        );
    }

    protected function getArguments(): array
    {
        return [
            new InputArgument('project', InputArgument::OPTIONAL, 'project name'),
        ];
    }

    protected function getOptions(): array
    {
        return [
            new InputOption('db-host', null, InputOption::VALUE_OPTIONAL, 'Change db host'),
        ];
    }
}
