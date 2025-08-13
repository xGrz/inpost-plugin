<?php

namespace Xgrz\InPost\Tests;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;
use Xgrz\InPost\InPostServiceProvider;

abstract class InPostTestCase extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('inpost.organization', '1916');
        $app['config']->set('inpost.token', 'INPOST-TOKEN');
        $app['config']->set('inpost.url', 'https://sandbox-api-shipx-pl.easypack24.net');
        $app['config']->set('inpost.widget', 'WIDGET-TOKEN');
        $app['config']->set('inpost.minimum_insurance_value', 500);
        $app['config']->set('inpost.synchronize_points_chunk_size', 500);
        $app['config']->set('inpost.cache.statuses.key', 'inpost-statuses');
        $app['config']->set('inpost.cache.statuses.ttl', 86400);
        $app['config']->set('inpost.cache.services.key', 'inpost-services');
        $app['config']->set('inpost.cache.services.ttl', 86400);
    }

    protected function getPackageProviders($app): array
    {
        return [
            InPostServiceProvider::class,
        ];
    }

    public function fakeOrganizationsResponse(): array
    {
        return ['*' => Http::response(self::fromFile('OrganizationResponse.json'))];
    }

    public function fakeStatusesResponse(): array
    {
        return ['*' => Http::response(self::fromFile('StatusesResponse.json'))];
    }

    public function fakeServicesResponse(): array
    {
        return ['*' => Http::response(self::fromFile('ServicesResponse.json'))];
    }

    public function fakeCostCentersListResponse(): array
    {
        return ['*' => Http::response(self::fromFile('CostCentersListResponse.json'))];
    }

    public function fakeCostCenterCreateResponse(): array
    {
        return ['*' => Http::response(self::fromFile('CostCenterCreateResponse.json'))];
    }

    public function fakeCostCenterDetailsResponse(): array
    {
        return ['*' => Http::response(self::fromFile('CostCenterDetailsResponse.json'))];
    }

    public function fakeCostCenterUpdateResponse(): array
    {
        return ['*' => Http::response(self::fromFile('CostCenterUpdateResponse.json'))];
    }




    protected function fromFile(string $file): array
    {
        return json_decode(file_get_contents(__DIR__ . '/JsonResponses/' . $file), true);
    }

}

