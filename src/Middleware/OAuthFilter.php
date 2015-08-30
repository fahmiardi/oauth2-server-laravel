<?php
/**
 * OAuth route filter
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Filters;

use LucaDegasperi\OAuth2Server\Authorizer;

class OAuthFilter
{
    /**
     * The Authorizer instance
     * @var \LucaDegasperi\OAuth2Server\Authorizer
     */
    protected $authorizer;

    /**
     * Whether or not to check the http headers only for an access token
     * @var bool
     */
    protected $httpHeadersOnly = false;

    /**
     * @param Authorizer $authorizer
     * @param bool $httpHeadersOnly
     */
    public function __construct(Authorizer $authorizer, $httpHeadersOnly = false)
    {
        if (! function_exists('getallheaders')) {
            function getallheaders() {
                foreach ($_SERVER as $key => $value) {
                    if (substr($key,0,5) == "HTTP_") {
                        $key = str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
                        $out[$key] = $value;
                    } else {
                        $out[$key] = $value;
                    }
                }
                return $out;
            }
        }
        
        $headers = getallheaders();
        $accessToken = isset($headers['Authorization']) ? trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $headers['Authorization'])) : null;
        
        $authorizer->validateAccessToken($this->httpHeadersOnly, $accessToken);
        
        $this->authorizer = $authorizer;
        $this->httpHeadersOnly = $httpHeadersOnly;
    }

    /**
     * Whether or not the filter should check only the http headers for an access token
     * @param $httpHeadersOnly
     */
    public function setHttpHeadersOnly($httpHeadersOnly)
    {
        $this->httpHeadersOnly = $httpHeadersOnly;
    }

    /**
     * The main filter method
     * @internal param mixed $route, mixed $request, mixed $owners,...
     * @return null
     * @throws \League\OAuth2\Server\Exception\AccessDeniedException
     */
    public function filter()
    {
        
    }
}