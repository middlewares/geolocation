<?php

namespace Middlewares\Tests;

use Middlewares\Geolocation;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class GeolocationTest extends TestCase
{
    public function testGeolocation()
    {
        $request = Factory::createServerRequest(['REMOTE_ADDR' => '123.9.34.23']);

        $response = Dispatcher::run([
            new Geolocation(),
            function ($request) {
                echo $request->getAttribute('client-location')->first()->getCountry();
            },
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals('China', (string) $response->getBody());
    }
}
