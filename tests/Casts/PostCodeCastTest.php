<?php

namespace Xgrz\InPost\Tests\Casts;

use Xgrz\InPost\Casts\PostCodeCast;
use Xgrz\InPost\Models\InPostPoint;
use Xgrz\InPost\Tests\InPostTestCase;

class PostCodeCastTest extends InPostTestCase
{

    public function test_can_cast_postal_code_to_database_format()
    {
        $cast = new PostCodeCast();
        $model = new InPostPoint();

        $this->assertEquals(
            '12345',
            $cast->set($model, 'post_code', '12-345', [])
        );

        $this->assertEquals(
            '01234',
            $cast->set($model, 'post_code', '01-234', [])
        );

        $this->assertEquals(
            '01234',
            $cast->set($model, 'post_code', '01234', [])
        );
    }

    public function test_invalid_post_code_throws_exception()
    {
        $this->expectException(\Xgrz\InPost\Exceptions\ShipXInvalidPostCodeException::class);
        $cast = new PostCodeCast();
        $model = new InPostPoint();

        $cast->set($model, 'post_code', 'A2-345', []);
    }

    public function test_can_cast_postal_code_to_display_format()
    {
        $cast = new PostCodeCast();
        $model = new InPostPoint();

        $this->assertEquals(
            '12-345',
            $cast->get($model, 'test', '12345', [])
        );

        $this->assertEquals(
            '01-234',
            $cast->get($model, 'test', '01234', [])
        );
    }

    public function test_can_cast_empty_post_code()
    {
        $cast = new PostCodeCast();
        $model = new InPostPoint();

        $this->assertEquals(
            '',
            $cast->set($model, 'test', null, [])
        );

        $this->assertEquals(
            null,
            $cast->set($model, 'test', null, [])
        );


    }

}
