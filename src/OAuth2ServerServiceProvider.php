<?php
/**
 * Service Provider for the OAuth 2.0 Server
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\Exception\OAuthException;
use LucaDegasperi\OAuth2Server\Filters\OAuthFilter;
use LucaDegasperi\OAuth2Server\Filters\OAuthScopeFilter;
use LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter;

class OAuth2ServerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     * @return void
     */
    public function boot()
    {
        $this->package('fahmiardi/oauth2-server-laravel', 'oauth2-server-laravel', __DIR__.'/');
        $this->registerErrorHandlers();
        $this->bootFilters();
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->registerAuthorizer();
        $this->registerFilterBindings();
    }

    /**
     * Register the Authorization server with the IoC container
     * @return void
     */
    public function registerAuthorizer()
    {
        $this->app->bindShared('oauth2-server.authorizer', function ($app) {
            $config = $app['config']->get('oauth2-server-laravel::oauth2');
            $checker = $app->make('League\OAuth2\Server\ResourceServer');

            $authorizer = new Authorizer($checker);
            $authorizer->setRequest($app['request']);
            $authorizer->setTokenType($app->make($config['token_type']));

            $app->refresh('request', $authorizer, 'setRequest');

            return $authorizer;
        });

        $this->app->bind('LucaDegasperi\OAuth2Server\Authorizer', function($app)
        {
            return $app['oauth2-server.authorizer'];
        });
    }

    /**
     * Register the Filters to the IoC container because some filters need additional parameters
     * @return void
     */
    public function registerFilterBindings()
    {
        $this->app->bindShared('LucaDegasperi\OAuth2Server\Filters\OAuthFilter', function ($app) {
            $httpHeadersOnly = $app['config']->get('oauth2-server-laravel::oauth2.http_headers_only');
            return new OAuthFilter($app['oauth2-server.authorizer'], $httpHeadersOnly);
        });

        $this->app->bindShared('LucaDegasperi\OAuth2Server\Filters\OAuthScopeFilter', function ($app) {
            $httpHeadersOnly = $app['config']->get('oauth2-server-laravel::oauth2.http_headers_only');
            return new OAuthScopeFilter($app['oauth2-server.authorizer'], $httpHeadersOnly);
        });

        $this->app->bindShared('LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter', function ($app) {
            $httpHeadersOnly = $app['config']->get('oauth2-server-laravel::oauth2.http_headers_only');
            return new OAuthOwnerFilter($app['oauth2-server.authorizer'], $httpHeadersOnly);
        });
    }

    /**
     * Get the services provided by the provider.
     * @return string[]
     * @codeCoverageIgnore
     */
    public function provides()
    {
        return ['oauth2-server.authorizer'];
    }
    

    /**
     * Boot the filters
     * @return void
     */
    private function bootFilters()
    {
        $this->app['router']->filter('oauth', 'LucaDegasperi\OAuth2Server\Filters\OAuthFilter');
        $this->app['router']->filter('oauth-scope', 'LucaDegasperi\OAuth2Server\Filters\OAuthScopeFilter');
        $this->app['router']->filter('oauth-owner', 'LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter');
    }

    /**
     * Register the OAuth error handlers
     * @return void
     */
    private function registerErrorHandlers()
    {
        $this->app->error(function(OAuthException $e) {
            return new JsonResponse([
                    'error' => $e->errorType,
                    'error_description' => $e->getMessage()
                ],
                $e->httpStatusCode,
                $e->getHttpHeaders()
            );
        });
    }
}
