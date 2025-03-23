<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Geocoder\Collection;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Provider\FreeGeoIp\FreeGeoIp;
use Http\Adapter\Guzzle7\Client;
use Middlewares\Geolocation;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class GeolocationTest extends TestCase
{
    /**
     * @return Geolocation
     */
    private static function getMiddleware()
    {
        return new class(new FreeGeoIp(new Client())) extends Geolocation {
            protected function getAddress(string $ip): Collection
            {
                assert($ip, '123.9.34.23');

                return new AddressCollection([
                    Address::createFromArray(['country' => 'China']),
                ]);
            }
        };
    }

    public function testGeolocation(): void
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

    public function testAttribute(): void
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
