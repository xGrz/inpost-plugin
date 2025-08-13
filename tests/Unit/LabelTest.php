<?php

use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Tests\InPostTestCase;

class LabelTest extends InPostTestCase
{

    public function test_can_call_api_for_download_label()
    {
        Http::fake($this->fakeServicesResponse());
        InPost::label('123456789');
        $type = config('inpost.label_type');
        $format = config('inpost.label_format');

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/shipments/123456789/label?format=' . $format . '&type=' . $type);;
    }
}
