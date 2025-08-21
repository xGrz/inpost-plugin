<?php

namespace Xgrz\InPost\Console\Commands;

use Illuminate\Console\Command;
use Xgrz\InPost\Jobs\SynchronizeInPostPointsJob;
use Xgrz\InPost\Models\InPostPoint;

class PointsCommand extends Command
{
    protected $signature = 'inpost:points';

    protected $description = 'Synchronize InPost Parcel-lockers and points of delivery';

    public function handle()
    {
        try {
            SynchronizeInPostPointsJob::dispatch()->onQueue('inpost');
            $this->call('queue:work', ['--queue' => 'inpost', '--stop-when-empty' => true]);

            $points = InPostPoint::count();

            $this->newLine();
            $this->components->twoColumnDetail('Points', $points);

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