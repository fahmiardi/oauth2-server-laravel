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
        $authorizer->validateAccessToken($this->httpHeadersOnly);
        
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