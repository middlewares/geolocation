<?php
declare(strict_types = 1);

namespace Middlewares;

use Geocoder\Collection;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
    public function __construct(Provider $provider)
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
            $address = $this->getAddress($ip);
            $request = $request->withAttribute($this->attribute, $address);
        }

        return $handler->handle($request);
    }

    /**
     * Get the address of an ip
     */
    protected function getAddress(string $ip): Collection
    {
        return $this->provider->geocodeQuery(GeocodeQuery::create($ip));
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
}
