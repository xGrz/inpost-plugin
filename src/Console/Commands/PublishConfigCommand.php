<?php

namespace Xgrz\InPost\Console\Commands;

use Illuminate\Console\Command;

class PublishConfigCommand extends Command
{
    protected $signature = 'inpost:publish-config';

    protected $description = 'Publish the InPost configuration file';

    public function handle(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'inpost-config',
        ]);
    }
}