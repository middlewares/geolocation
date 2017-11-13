<?php
declare(strict_types = 1);

namespace Middlewares;

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
     * @var Provider
     */
    private $provider;

    /**
     * @var string|null
     */
    private $ipAttribute;

    /**
     * @var string The attribute name
     */
    private $attribute = 'client-location';

    /**
     * Set the provider instance.
     */
    public function __construct(Provider $provider = null)
    {
        $this->provider = $provider;
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
            $provider = $this->provider ?: self::createProvider();
            $address = $provider->geocodeQuery(GeocodeQuery::create($ip));
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
     * Generate the default provider.
     */
    private static function createProvider(): Provider
    {
        return new FreeGeoIp(new Client());
    }
}
