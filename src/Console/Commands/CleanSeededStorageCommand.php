<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

/**
 * @deprecated Usage is deprecated and will be removed in 3.0.
 */
class CleanSeededStorageCommand extends Command
{
    protected $signature = 'hd-admin:utils:clean-seed-files';
    protected $description = 'Clean your storage from seeded files before running seeds';

    public function handle(): int
    {
        trigger_deprecation('hexide-digital/hexide-admin-panel', '2.9.2', 'Usage "%s" is deprecated and will be removed in 3.0.', __CLASS__);

        if (!App::isLocal()) {
            $this->error('You can`t run in non locally environment');

            return self::FAILURE;
        }

        $this->info('Start of deleting files.');

        $storage = Storage::disk('public');

        // $storage->deleteDir('seeded');

        $storage->createDir('seeded');

        $files = $storage->files('seeded');

        $storage->delete($files);

        $this->info('Files deleted, count: ' . count($files));

        return self::SUCCESS;
    }
}
