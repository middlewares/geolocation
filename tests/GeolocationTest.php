<?php

namespace Middlewares\Tests;

use Middlewares\Geolocation;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

class GeolocationTest extends \PHPUnit_Framework_TestCase
{
    public function testGeolocation()
    {
        $request = Factory::createServerRequest(['REMOTE_ADDR' => '123.9.34.23']);

        $response = (new Dispatcher([
            new Geolocation(),
            function ($request) {
                echo $request->getAttribute('client-location')->first()->getCountry();
            },
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals('China', (string) $response->getBody());
    }
}
