# middlewares/geolocation

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabs Insight][ico-sensiolabs]][link-sensiolabs]

Middleware to geolocate the client using the ip address and [Geocoder](https://github.com/geocoder-php/Geocoder) and save the result as a request attribute.

**Note:** This middleware is intended for server side only

## Requirements

* PHP >= 5.6
* A [PSR-7](https://packagist.org/providers/psr/http-message-implementation) http mesage implementation ([Diactoros](https://github.com/zendframework/zend-diactoros), [Guzzle](https://github.com/guzzle/psr7), [Slim](https://github.com/slimphp/Slim), etc...)
* A [PSR-15](https://github.com/http-interop/http-middleware) middleware dispatcher ([Middleman](https://github.com/mindplay-dk/middleman), etc...)

## Installation

This package is installable and autoloadable via Composer as [middlewares/geolocation](https://packagist.org/packages/middlewares/geolocation).

```sh
composer require middlewares/geolocation
```

## Example

```php
$dispatcher = new Dispatcher([
	new Middlewares\Geolocation(),

    function ($request) {
        //Get the client location
        $location = $request->getAttribute('client-location');

        $country = $location->first()->getCountry();
    }
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

## Options

#### `__construct(Geocoder\Geocoder $geocoder = null)`

The geocoder object used to geolocate the client. If it's not provided, use a generic `Geocoder\Provider\FreeGeoIp` instance.

It's also recommended to configure it to [caching responses.](https://github.com/geocoder-php/Geocoder/blob/master/docs/cookbook/cache.md)

#### `ipAttribute(string $ipAttribute)`

By default uses the `REMOTE_ADDR` server parameter to get the client ip. This option allows to use a request attribute. Useful to combine with a ip detection middleware, for example [client-ip](https://github.com/middlewares/client-ip).

#### `attribute(string $attribute)`

The attribute name used to store the client addresses in the server request. By default is `client-location`.

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/geolocation.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/geolocation/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/geolocation.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/geolocation.svg?style=flat-square
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/36786f5a-2a15-4399-8817-8f24fcd8c0b4.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/geolocation
[link-travis]: https://travis-ci.org/middlewares/geolocation
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/geolocation
[link-downloads]: https://packagist.org/packages/middlewares/geolocation
[link-sensiolabs]: https://insight.sensiolabs.com/projects/36786f5a-2a15-4399-8817-8f24fcd8c0b4
