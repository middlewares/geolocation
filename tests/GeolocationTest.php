<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Geocoder\Provider\FreeGeoIp\FreeGeoIp;
use Http\Adapter\Guzzle6\Client;
use Middlewares\Geolocation;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class GeolocationTest extends TestCase
{
    public function testGeolocation()
    {
        //do not use http due travis:
        //GuzzleHttp\Exception\ConnectException: cURL error 35: gnutls_handshake() failed:
        //Handshake failed (see http://curl.haxx.se/libcurl/c/libcurl-errors.html)
        $geocoder = new FreeGeoIp(new Client(), 'http://freegeoip.net/json/%s');

        $response = Dispatcher::run(
            [
                new Geolocation($geocoder),
                function ($request) {
                    echo $request->getAttribute('client-location')->first()->getCountry();
                },
            ],
            Factory::createServerRequest('GET', '/', ['REMOTE_ADDR' => '123.9.34.23'])
        );

        $this->assertEquals('China', (string) $response->getBody());
    }

    public function testAttribute()
    {
        $geocoder = new FreeGeoIp(new Client(), 'http://freegeoip.net/json/%s');

        $response = Dispatcher::run(
            [
                (new Geolocation($geocoder))
                    ->attribute('foo')
                    ->ipAttribute('bar'),

                function ($request) {
                    echo $request->getAttribute('foo')->first()->getCountry();
                },
            ],
            Factory::createServerRequest('GET', '/')
                ->withAttribute('bar', '123.9.34.23')
        );

        $this->assertEquals('China', (string) $response->getBody());
    }
}
