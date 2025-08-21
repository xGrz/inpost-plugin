<?php

namespace Xgrz\InPost\Console\Commands;

use Illuminate\Console\Command;

class PublishMigrationsCommand extends Command
{
    protected $signature = 'inpost:publish-migrations';

    protected $description = 'Publish the InPost plugin migrations';

    public function handle(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'inpost-migrations',
        ]);
    }
}