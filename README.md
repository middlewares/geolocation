# middlewares/geolocation

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
![Testing][ico-ga]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabs Insight][ico-sensiolabs]][link-sensiolabs]

Middleware to geolocate the client using the ip address and [Geocoder](https://github.com/geocoder-php/Geocoder) and save the result as a request attribute.

## Requirements

* PHP >= 7.0
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)
* A [PHP-HTTP adapter](http://docs.php-http.org/en/latest/clients.html), for example [Guzzle6](https://github.com/php-http/guzzle6-adapter)
* A [Geocoder ip-based provider](https://github.com/geocoder-php/Geocoder#ip)

## Installation

This package is installable and autoloadable via Composer as [middlewares/geolocation](https://packagist.org/packages/middlewares/geolocation).

```sh
composer require middlewares/geolocation
```

## Example

```php
$freeGeoIpProvider = new Geocoder\Provider\FreeGeoIp($adapter);

$dispatcher = new Dispatcher([
    new Middlewares\Geolocation($freeGeoIpProvider),

    function ($request) {
        //Get the client location
        $location = $request->getAttribute('client-location');

        $country = $location->first()->getCountry();
    }
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

## Options

#### `__construct(Geocoder\Provider\Provider $provider)`

The geocoder provider used to geolocate the client.

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
[ico-ga]: https://github.com/middlewares/geolocation/workflows/testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/geolocation.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/geolocation
[link-downloads]: https://packagist.org/packages/middlewares/geolocation
[link-sensiolabs]: https://insight.sensiolabs.com/projects/b6c8bd3a-b3da-45ec-b2ac-9d27ae390b1b
