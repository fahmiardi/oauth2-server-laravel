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

use League\OAuth2\Server\Exception\InvalidScopeException;

class OAuthScopeFilter extends OAuthFilter
{
    /**
     * The scopes to check for
     * @var array
     */
    protected $scopes = [];

    /**
     * Run the oauth filter
     *
     * @internal param mixed $route, mixed $request, mixed $scope,...
     * @return void a bad response in case the request is invalid
     */
    public function filter()
    {
        if (func_num_args() > 2) {
            $args = func_get_args();
            $this->scopes = array_slice($args, 2);
        }

        if (! empty($this->scopes)) {

            foreach ($this->scopes as $scope) {

                if (! $this->authorizer->hasScope($scope)) {
                    throw new InvalidScopeException(implode(', ', $this->scopes));
                }

            }

        }
    }
}
