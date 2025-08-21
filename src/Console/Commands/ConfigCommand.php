<?php

namespace Xgrz\InPost\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Xgrz\InPost\Config\InPostConfig;
use Xgrz\InPost\Models\ParcelTemplate;

class ConfigCommand extends Command
{
    protected $signature = 'inpost:config';

    protected $description = 'Show the InPost configuration file';

    public function handle(): void
    {
        $config = config('inpost');
        $webhook = InPostConfig::webhookFullUrl();
        $templates = ParcelTemplate::count();
        try {
            $sandbox = Str::of(InPostConfig::baseUrl())->contains('sandbox') ? 'Sandbox' : 'Production';
        } catch (\Exception $e) {
            $sandbox = 'Not configured';
        }

        $this->components->twoColumnDetail('Webhook url', $webhook);
        $this->components->twoColumnDetail('Plugin environment', $sandbox);
        $this->components->twoColumnDetail('Parcel templates stored', $templates);
        $this->components->twoColumnDetail('Organization ID', $config['organization']);
        $this->components->twoColumnDetail('API Token', $config['token'] ? 'Configured' : 'Not configured');
        $this->components->twoColumnDetail('Minimum value for insurance', $config['minimum_insurance_value']);
        $this->components->twoColumnDetail('Label type', $config['label_type'] ?? 'Not configured');
        $this->components->twoColumnDetail('Label format', $config['label_format'] ?? 'Not configured');
    }

}