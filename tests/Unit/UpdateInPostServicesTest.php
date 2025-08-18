<?php

namespace Xgrz\InPost\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Xgrz\InPost\Tests\InPostTestCase;

class UpdateInPostServicesTest extends InPostTestCase
{
    use RefreshDatabase;

    public function test_can_update_inpost_services()
    {
        $job = new \Xgrz\InPost\Jobs\UpdateInPostServicesJob();
        $job->handle();

        $this->assertDatabaseCount('inpost_services', 15);
        $this->assertDatabaseCount('inpost_additional_services', 61);
    }
}