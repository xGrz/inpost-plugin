<?php

namespace Xgrz\InPost\Console\Commands;

use Illuminate\Console\Command;
use Xgrz\InPost\Jobs\UpdateInPostServicesJob;
use Xgrz\InPost\Models\InPostAdditionalService;
use Xgrz\InPost\Models\InPostService;

class ServicesCommand extends Command
{
    protected $signature = 'inpost:services';

    protected $description = 'Synchronize InPost services';

    public function handle()
    {
        try {
            UpdateInPostServicesJob::dispatch()->onQueue('inpost');
            $this->call('queue:work', ['--queue' => 'inpost', '--stop-when-empty' => true]);

            $services = InPostService::count();
            $additionalServices = InPostAdditionalService::count();
            $this->newLine();

            $this->components->twoColumnDetail('Services', $services);
            $this->components->twoColumnDetail('Additional services', $additionalServices);

            $this->newLine();
            $this->info('Synchronized');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            $this->newLine();
            $this->warn('Not synchronized');
            return Command::FAILURE;
        }

    }
    
}