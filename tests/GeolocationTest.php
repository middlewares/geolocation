<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Eloquent\Phony\Phpunit\Phony;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Provider\FreeGeoIp\FreeGeoIp;
use Http\Adapter\Guzzle6\Client;
use Middlewares\Geolocation;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class GeolocationTest extends TestCase
{
    private static function getMiddleware()
    {
        $container = Phony::partialMock(Geolocation::class, [new FreeGeoIp(new Client())]);
        $container->getAddress->with('123.9.34.23')->returns(new AddressCollection([
            Address::createFromArray(['country' => 'China']),
        ]));

        return $container->get();
    }

    public function testGeolocation()
    {
        $response = Dispatcher::run(
            [
                $this->getMiddleware(),
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
                $this->getMiddleware()
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
