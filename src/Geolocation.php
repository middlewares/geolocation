<?php
declare(strict_types = 1);

namespace Middlewares;

use Geocoder\Geocoder;
use Geocoder\Provider\FreeGeoIp\FreeGeoIp;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Http\Adapter\Guzzle6\Client;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Geolocation implements MiddlewareInterface
{
    /**
     * @var Geocoder
     */
    private $geocoder;

    /**
     * @var string|null
     */
    private $ipAttribute;

    /**
     * @var string The attribute name
     */
    private $attribute = 'client-location';

    /**
     * Set the geocoder instance.
     */
    public function __construct(Geocoder $geocoder = null)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * Set the attribute name to get the client ip.
     */
    public function ipAttribute(string $ipAttribute): self
    {
        $this->ipAttribute = $ipAttribute;

        return $this;
    }

    /**
     * Set the attribute name to store the geolocation info.
     */
    public function attribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ip = $this->getIp($request);

        if (!empty($ip)) {
            $geocoder = $this->geocoder ?: self::createGeocoder();
            $address = $geocoder->geocodeQuery(GeocodeQuery::create($ip));
            $request = $request->withAttribute($this->attribute, $address);
        }

        return $handler->handle($request);
    }

    /**
     * Get the client ip.
     */
    private function getIp(ServerRequestInterface $request): string
    {
        $server = $request->getServerParams();

        if ($this->ipAttribute !== null) {
            return $request->getAttribute($this->ipAttribute);
        }

        return isset($server['REMOTE_ADDR']) ? $server['REMOTE_ADDR'] : '';
    }

    /**
     * Generate the default geocoder provider.
     */
    private static function createGeocoder(): Provider
    {
        return new FreeGeoIp(new Client());
    }
}
