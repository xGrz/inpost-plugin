<?php

namespace Xgrz\InPost\Console\Commands;

use Illuminate\Console\Command;
use Xgrz\InPost\Actions\SynchronizePointsAction;
use Xgrz\InPost\Models\InPostPoint;

class ResetPointsCommand extends Command
{
    protected $signature = 'inpost:reset-points';

    protected $description = 'Synchronize InPost Parcel-lockers and points of delivery with reset';

    public function handle(): int
    {
        try {
            SynchronizePointsAction::dispatch('inpost', true);
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