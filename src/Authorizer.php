<?php
/**
 * Laravel Service Provider for the OAuth 2.0 Server
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server;

use League\OAuth2\Server\ResourceServer as Checker;
use League\OAuth2\Server\TokenType\TokenTypeInterface;
use Symfony\Component\HttpFoundation\Request;

class Authorizer
{

    /**
     * The resource server (aka the checker)
     * @var \League\OAuth2\Server\ResourceServer
     */
    protected $checker;

    /**
     * Create a new Authorizer instance
     * @param Checker $checker
     */
    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    /**
     * @return \League\OAuth2\Server\ResourceServer
     */
    public function getChecker()
    {
        return $this->checker;
    }

    /**
     * Validate a request with an access token in it
     * @param bool $httpHeadersOnly whether or not to check only the http headers of the request
     * @param string|null $accessToken an access token to validate
     * @return mixed
     */
    public function validateAccessToken($httpHeadersOnly = false, $accessToken = null)
    {
        $this->checker->isValidRequest($httpHeadersOnly, $accessToken);
    }

    /**
     * get the scopes associated with the current request
     * @return array
     */
    public function getScopes()
    {
        return $this->checker->getAccessToken()->getScopes();
    }

    /**
     * Check if the current request has all the scopes passed
     * @param string|array $scope the scope(s) to check for existence
     * @return bool
     */
    public function hasScope($scope)
    {
        return $this->checker->getAccessToken()->hasScope($scope);
    }

    /**
     * Get the resource owner ID of the current request
     * @return string
     */
    public function getResourceOwnerId()
    {
        return $this->checker->getAccessToken()->getSession()->getOwnerId();
    }

    /**
     * Get the resource owner type of the current request (client or user)
     * @return string
     */
    public function getResourceOwnerType()
    {
        return $this->checker->getAccessToken()->getSession()->getOwnerType();
    }

    /**
     * get the client id of the current request
     * @return string
     */
    public function getClientId()
    {
        return $this->checker->getAccessToken()->getSession()->getClient()->getId();
    }

    /**
     * Set the request to use on the issuer and checker
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->checker->setRequest($request);
    }

    /**
     * Set the token type to use
     * @param \League\OAuth2\Server\TokenType\TokenTypeInterface $tokenType
     */
    public function setTokenType(TokenTypeInterface $tokenType)
    {
        $this->checker->setTokenType($tokenType);
    }
}
