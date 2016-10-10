<?php

namespace Middlewares\Tests;

use Middlewares\Geolocation;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;
use mindplay\middleman\Dispatcher;

class GeolocationTest extends \PHPUnit_Framework_TestCase
{
    public function testGeolocation()
    {
        $request = new ServerRequest(['REMOTE_ADDR' => '123.9.34.23']);

        $response = (new Dispatcher([
            new Geolocation(),
            function ($request) {
                $address = $request->getAttribute('client-location');

                $response = new Response();
                $response->getBody()->write($address->first()->getCountry());

                return $response;
            },
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals('China', (string) $response->getBody());
    }
}
