<?php

namespace Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Geocoder\Geocoder;
use Geocoder\Provider\FreeGeoIp;
use Ivory\HttpAdapter\FopenHttpAdapter;

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
     * Constructor. Set the geocoder instance.
     *
     * @param null|Geocoder $geocoder
     */
    public function __construct(Geocoder $geocoder = null)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * Set the attribute name to get the client ip.
     *
     * @param string $ipAttribute
     *
     * @return self
     */
    public function ipAttribute($ipAttribute)
    {
        $this->ipAttribute = $ipAttribute;

        return $this;
    }

    /**
     * Set the attribute name to store the geolocation info.
     *
     * @param string $attribute
     *
     * @return self
     */
    public function attribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Process a server request and return a response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $ip = $this->getIp($request);

        if (!empty($ip)) {
            $geocoder = $this->geocoder ?: self::createGeocoder();
            $address = $geocoder->geocode($ip);
            $request = $request->withAttribute($this->attribute, $address);
        }

        return $delegate->process($request);
    }

    /**
     * Get the client ip.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    private function getIp(ServerRequestInterface $request)
    {
        $server = $request->getServerParams();

        if ($this->ipAttribute !== null) {
            return $request->getAttribute($this->ipAttribute);
        }

        return isset($server['REMOTE_ADDR']) ? $server['REMOTE_ADDR'] : '';
    }

    /**
     * Generate a default geocoder.
     *
     * @return Geocoder
     */
    private static function createGeocoder()
    {
        return new FreeGeoIp(new FopenHttpAdapter());
    }
}
