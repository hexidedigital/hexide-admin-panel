<?php

namespace HexideDigital\HexideAdmin\Console\Commands;

use App\Console\Commands\BaseCommand;
use ErrorException;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class PrepareDeployCommand extends BaseCommand
{
    protected $name = 'hd-admin:project:deploy';
    protected $description = 'Command description';

    protected int $step = 1;
    protected ProgressBar $bar;

    /** @var resource */
    protected $resourceDeployFile;
    protected string $projectDir = '.';
    protected string $accessYamlPath = 'deploy/access.yml';
    protected string $stageName = 'dev';
    protected string $sshDir = '.ssh';

    protected array $replaceMap = [];
    protected array $gitlabVars = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->resourceDeployFile = fopen(base_path('deploy/dep.txt'), 'w');

        $this->bar = $this->output->createProgressBar();
        $this->bar->start();

        try {
            $this->setupVariablesAndConfigs();

            $this->executeAndPrintDeployCommands();
        } catch (Exception $exception) {
            $this->error(get_class($exception));
            $this->error($exception->getMessage());
        } finally {
            fclose($this->resourceDeployFile);
            $this->bar->finish();
        }

        $this->newLine();

        return self::SUCCESS;
    }

    private function parseAccess(): array
    {
        $accessFile = Yaml::parseFile($this->projectDir . '/deploy/access.yml');

        $this->stageName = $accessFile['stage'];

        return Arr::get($accessFile, 'access.' . $this->stageName, []);
    }

    private function setupVariablesAndConfigs(): void
    {
        $this->newSection('setup variable and configs');

        $stageConfigs = $this->parseAccess();

        $server = Arr::get($stageConfigs, 'server', []);
        $database = Arr::get($stageConfigs, 'database', []);
        $mail = Arr::get($stageConfigs, 'mail', []);
        $options = Arr::get($stageConfigs, 'options', []);

        $this->replaceMap = [
            '{{PROJ_DIR}}' => '.',

            '{{DEPLOY_BASE_DIR}}' => $this->replace(
                $options['base-dir-pattern'], [
                    '{{USER}}' => $server['login'],
                    '{{HOST}}' => $server['host'],
                ]
            ),

            '{{DEPLOY_SERVER}}' => $server['host'],
            '{{DEPLOY_USER}}'   => $server['login'],
            '{{DEPLOY_PASS}}'   => $server['password'],

            '{{DB_DATABASE}}' => $database['database'],
            '{{DB_USERNAME}}' => $database['username'],
            '{{DB_PASSWORD}}' => $database['password'],

            '{{MAIL_HOSTNAME}}' => $mail['hostname'] ?? '',
            '{{MAIL_USER}}'     => $mail['username'] ?? '',
            '{{MAIL_PASSWORD}}' => $mail['password'] ?? '',

            '{{CI_REPOSITORY_URL}}'  => $options['git-url'],
            '{{CI_COMMIT_REF_NAME}}' => $this->stageName,

            '{{BIN_PHP}}'      => $options['bin-php'],
            '{{BIN_COMPOSER}}' => $options['bin-composer'],
        ];

        $this->gitlabVars = [
            'BIN_PHP'      => $this->replace('{{BIN_PHP}}'),
            'BIN_COMPOSER' => $this->replace('{{BIN_COMPOSER}}'),

            'DEPLOY_BASE_DIR' => $this->replace('{{DEPLOY_BASE_DIR}}'),
            'DEPLOY_SERVER'   => $this->replace('{{DEPLOY_SERVER}}'),
            'DEPLOY_USER'     => $this->replace('{{DEPLOY_USER}}'),

            'SSH_PRIVATE_KEY' => '-----BEGIN OPENSSH PRIVATE ',
        ];
    }

    private function executeAndPrintDeployCommands()
    {
        $this->generateLocalDeploySshKeys();
        $this->copySshKeysOnRemoteHost();
        $this->generateRemoteHostSshKeys();
        $this->processAndPrintGitlabVariables();
        $this->addGitlabToKnownHostsOnRemoteHost();

        $this->runDeployPrepareCommand();
        $this->prepareAndCopyDotEnvFileForRemote();
        $this->runFirstDeployCommand();

        $this->insertCustomAliasesOnRemoteHost();
        $this->ideaSetup();
    }

    private function generateLocalDeploySshKeys(): void
    {
        $this->newSection('generate ssh keys - private key to gitlab (local)');

        $this->sshDir = $this->replace('{{PROJ_DIR}}/.ssh_{{CI_COMMIT_REF_NAME}}');

        $folderExists = is_dir($this->sshDir);

        if (!$folderExists) {
            mkdir($this->sshDir);
        }

        if ($folderExists && (file_exists($this->sshDir . '/id_rsa') || file_exists($this->sshDir . '/id_rsa.pub'))
            && !$this->confirmAction('Should generate keys command?', false)) {
            return;
        }

        $this->runProcessCommand("ssh-keygen -t rsa -f $this->sshDir/id_rsa -N \"\" -y");

        $this->appendEchoLine("cat $this->sshDir/id_rsa", 'info');
        $this->gitlabVars['SSH_PRIVATE_KEY'] = $this->getContent($this->sshDir . '/id_rsa');
    }

    private function copySshKeysOnRemoteHost(): void
    {
        $this->newSection('copy ssh to server - public key to remote host');
        $this->appendEchoLine($this->replace('can ask a password - enter <comment>{{DEPLOY_PASS}}</comment>'));

        $this->runProcessCommand("ssh-copy-id -i $this->sshDir/id_rsa.pub {{DEPLOY_USER}}@{{DEPLOY_SERVER}}");
    }

    private function generateRemoteHostSshKeys(): void
    {
        $this->newSection('Generate generate ssh-keys on remote host');

        $sshRemote = $this->replace("ssh -i $this->sshDir/id_rsa {{DEPLOY_USER}}@{{DEPLOY_SERVER}} ");

        if ($this->confirmAction('Generate ssh keys on remote host', false)) {
            $this->runProcessCommand($sshRemote . '"ssh-keygen -t rsa -f .ssh/id_rsa -N \"\""');
        }

        $this->runProcessCommand($sshRemote . '"cat ~/.ssh/id_rsa.pub"', function ($type, $buffer) {
            $this->gitlabVars['SSH_PUB_KEY'] = $buffer;
        });
    }

    private function processAndPrintGitlabVariables(): void
    {
        $this->newSection('gitlab variables');

        $rows = [];
        foreach (Arr::except($this->gitlabVars, 'SSH_PRIVATE_KEY') as $key => $val) {
            $this->writeToFile($key . PHP_EOL . $val . PHP_EOL);

            $rows[] = [$key, $val];
        }

        $this->appendEchoLine('SSH_PRIVATE_KEY');
        $this->appendEchoLine(Arr::get($this->gitlabVars, 'SSH_PRIVATE_KEY', ''));

        $this->table(['key', 'value'], $rows);

        $this->appendEchoLine("SSH_PUB_KEY => Gitlab.project -> Settings -> Repository -> Deploy keys");

        if (!$this->option('only-print')) {
            $this->ask('Now, setup gitlab variables. When it`s ready, press enter');
        }
    }

    private function addGitlabToKnownHostsOnRemoteHost(): void
    {
        $this->newSection('add gitlab to confirmed (known hosts) on remote host');

        if (!$this->confirmAction('Append gitlab IP to remote host known_hosts file?', true)) {
            return;
        }

        $knownHost = '';
        $this->runProcessCommand('ssh-keyscan -t ecdsa-sha2-nistp256 gitlab.hexide-digital.com,188.34.141.230',
            function ($type, $buffer) use (&$knownHost) {
                $knownHost = trim($buffer);
            }
        );

        // todo before append, check if remote host already know about gitlab
        $this->runProcessCommand("ssh -i $this->sshDir/id_rsa {{DEPLOY_USER}}@{{DEPLOY_SERVER}} echo \"$knownHost\" >> ~/.ssh/known_hosts");
    }

    private function runDeployPrepareCommand(): void
    {
        $this->newSection('run deploy prepare');

        if (!$this->confirmAction('Should run deploy:prepare command?', false)) {
            return;
        }

        $deployFile = $this->projectDir . '/deploy.php';

        $initialContent = $this->getContent($deployFile);
        if (empty($initialContent)) {
            throw new Exception('Deploy file is empty or not exists.');
        }

        $this->putNewVariablesToDeployFile($deployFile);

        $this->runProcessCommand('php {{PROJ_DIR}}/vendor/bin/dep deploy:prepare {{CI_COMMIT_REF_NAME}} -v -o branch={{CI_COMMIT_REF_NAME}}',
            function ($type, $buffer) {
                $this->line($type . ' > ' . trim($buffer));
            }
        );

        $this->rollbackDeployFileContent($deployFile, $initialContent);
    }

    private function putNewVariablesToDeployFile(string $deployFile): void
    {
        $vars = <<<PHP
\$CI_REPOSITORY_URL = "{{CI_REPOSITORY_URL}}";
\$CI_COMMIT_REF_NAME = "{{CI_COMMIT_REF_NAME}}";
\$BIN_PHP = "{{BIN_PHP}}";
\$BIN_COMPOSER = "{{BIN_COMPOSER}}";
\$DEPLOY_BASE_DIR = "{{DEPLOY_BASE_DIR}}";
\$DEPLOY_SERVER = "{{DEPLOY_SERVER}}";
\$DEPLOY_USER = "{{DEPLOY_USER}}";
PHP;

        $this->putContentToFile($deployFile, [
            "/*CI_ENV*/"           => $this->replace($vars),
            '->user($DEPLOY_USER)' => '->user($DEPLOY_USER)' . PHP_EOL . "    ->identityFile('./.ssh/id_rsa')",
        ]);
    }

    private function rollbackDeployFileContent(string $deployFile, string $initialContent): void
    {
        file_put_contents($deployFile, $initialContent);
        $this->putContentToFile($deployFile, [
            '->user($DEPLOY_USER)' . PHP_EOL . "    ->identityFile('./.ssh/id_rsa')" => '->user($DEPLOY_USER)',
        ]);
    }

    private function prepareAndCopyDotEnvFileForRemote(): void
    {
        $this->newSection('setup .env.host and move to server');

        $this->runProcessCommand("mv .env .env.local");
        $this->runProcessCommand("cp .env.example .env");

        $ENV = [
            'APP_URL=http://localhost:8000' => $this->replace('APP_URL=https://{{DEPLOY_SERVER}}'),
            'DB_DATABASE=laravel_database'  => $this->replace('DB_DATABASE={{DB_DATABASE}}'),
            'DB_USERNAME=laravel_database'  => $this->replace('DB_USERNAME={{DB_USERNAME}}'),
            'DB_PASSWORD=laravel_password'  => $this->replace('DB_PASSWORD={{DB_PASSWORD}}'),

            "MAIL_HOST=mailhog"      => $this->replace("MAIL_HOST={{MAIL_HOSTNAME}}"),
            "MAIL_PORT=1025"         => "MAIL_PORT=587",
            "MAIL_USERNAME=null"     => $this->replace("MAIL_USERNAME={{MAIL_USER}}"),
            "MAIL_PASSWORD=null"     => $this->replace("MAIL_PASSWORD={{MAIL_PASSWORD}}"),
            "MAIL_ENCRYPTION=null"   => "MAIL_ENCRYPTION=tls",
            "MAIL_FROM_ADDRESS=null" => $this->replace("MAIL_FROM_ADDRESS={{MAIL_USER}}"),
        ];

        $this->putContentToFile($this->projectDir . '/.env', $ENV);

        Artisan::call('key:generate', [], $this->output);
        Artisan::call('jwt:secret -f', [], $this->output);

        $this->runProcessCommand("scp -i $this->sshDir/id_rsa $this->sshDir/.env {{DEPLOY_USER}}@{{DEPLOY_SERVER}}:{{DEPLOY_BASE_DIR}}/shared/.env");

        $this->runProcessCommand("mv .env.local .env");
    }

    private function runFirstDeployCommand(): void
    {
        $this->newSection('run deploy from local');

        $this->runProcessCommand('php {{PROJ_DIR}}/vendor/bin/dep deploy {{CI_COMMIT_REF_NAME}} -v -o branch={{CI_COMMIT_REF_NAME}}',
            function ($type, $buffer) {
                $this->line($type . ' > ' . trim($buffer));
            }
        );
    }

    private function insertCustomAliasesOnRemoteHost(): void
    {
        if ($this->option('aliases')) {
            $this->newSection('append custom aliases');

            $CONTENT = <<<BASH
_artisan()
{
    local arg=\"\${COMP_LINE#php }\"

    case \"\$arg\" in
        artisan*)
            COMP_WORDBREAKS=\${COMP_WORDBREAKS//:}
            COMMANDS=`php74 artisan --raw --no-ansi list | sed \"s/[[:space:]].*//g\"`
            COMPREPLY=(`compgen -W \"\$COMMANDS\" -- \"\${COMP_WORDS[COMP_CWORD]}\"`)
            ;;
        *)
            COMPREPLY=( \$(compgen -o default -- \"\${COMP_WORDS[COMP_CWORD]}\") )
            ;;
        esac

    return 0
}
complete -F _artisan artisan
complete -F _artisan php74

alias artisan=\"php74 artisan\"
alias pcomposer=\"php74 /usr/bin/composer\"
BASH;

            $this->runProcessCommand("ssh -i $this->sshDir/id_rsa {{DEPLOY_USER}}@{{DEPLOY_SERVER}} 'echo \"$CONTENT\" >> ~/.bashrc'");
        }
    }

    private function ideaSetup(): void
    {
        $this->newSection('IDEA - Phpstorm');

        $this->appendEchoLine($this->replace(<<<TEXT
 - change mount path
 <info>{{DEPLOY_BASE_DIR}}</info>

 - add site url
 <info>{{DEPLOY_SERVER}}</info>

 - add mapping
 <info>/current</info>

 - connect to databases (local and remote)
 TEXT
        ));

    }


    // --------------- output --------------

    private function newSection(string $name): void
    {
        $this->appendEchoLine(PHP_EOL . '-----------------------------', 'comment');
        $this->appendEchoLine($this->step++ . '. ' . Str::ucfirst($name) . PHP_EOL, 'comment');
        $this->bar->advance();
    }

    private function appendEchoLine(?string $content, string $style = null): void
    {
        $this->writeToFile($content);
        $this->writeToConsole($content, $style);
    }

    private function writeToConsole(?string $content, string $style = null): void
    {
        $this->line($content ?? '', $style);
    }

    private function writeToFile(?string $content): void
    {
        fwrite($this->resourceDeployFile, $content . PHP_EOL);
    }

    private function getContent(string $filename, string $default = null): ?string
    {
        try {
            $content = file_get_contents($filename);
        } catch (ErrorException $exception) {
            $this->warn('Failed to open file: ' . $filename);
            $content = $default;
        }

        return $content;
    }


    // --------------- content processing --------------

    private function confirmAction(string $question, bool $default = false): bool
    {
        return $this->option('force') || !$this->confirm($question, $default);
    }

    private function runProcessCommand(string $command, callable $callable = null): void
    {
        $command = $this->replace($command);

        $this->appendEchoLine($command, 'info');

        if ($this->option('only-print')) {
            return;
        }

        $this->line('running command...');
        $process = Process::fromShellCommandline($command);
        $process->run($callable);
    }

    private function replace(string $content, array $replaceMap = null): string
    {
        $replaceMap = is_null($replaceMap) ? $this->replaceMap : $replaceMap;

        return str_replace(array_keys($replaceMap), array_values($replaceMap), $content);
    }

    private function putContentToFile(string $file, array $replace = null): void
    {
        $content = $this->replace(file_get_contents($file), $replace);

        file_put_contents($file, $content);
    }

    // --------------- command info --------------

    protected function getArguments(): array
    {
        return [
        ];
    }

    protected function getOptions(): array
    {
        return [
            new InputOption('force', 'f', InputOption::VALUE_NONE, 'Confirm all choices and force all commands'),
            new InputOption('aliases', null, InputOption::VALUE_NONE, 'Append custom aliases for artisan and php'),
            new InputOption('only-print', null, InputOption::VALUE_NONE, 'Only print commands, with-out executing commands'),
        ];
    }
}
