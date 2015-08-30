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

use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\Exception\OAuthException;
// use LucaDegasperi\OAuth2Server\Filters\OAuthFilter;
// use LucaDegasperi\OAuth2Server\Filters\OAuthScopeFilter;
// use LucaDegasperi\OAuth2Server\Filters\OAuthOwnerFilter;
use LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware;
use League\OAuth2\Server\ResourceServer;

class OAuth2ServerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    public function setupConfig()
    {
        $source = realpath(__DIR__.'/config/oauth2.php');
        $this->publishes([$source => config_path('oauth2.php')]);
        $this->mergeConfigFrom($source, 'oauth2');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAuthorizer();
        $this->registerMiddlewareBindings();
    }

    /**
     * Register the Authorization server with the IoC container
     * @return void
     */
    public function registerAuthorizer()
    {

        $this->app->singleton('oauth2-server.authorizer', function ($app) {
            $config = config('oauth2');
            $checker = $app->make(ResourceServer::class);

            $authorizer = new Authorizer($checker);
            $authorizer->setRequest($app['request']);
            $authorizer->setTokenType($app->make($config['token_type']));

            $app->refresh('request', $authorizer, 'setRequest');

            return $authorizer;
        });

        $this->app->bind(Authorizer::class, function ($app) {
            return $app['oauth2-server.authorizer'];
        });
    }

    /**
     * Register the Filters to the IoC container because some filters need additional parameters
     * @return void
     */
    public function registerMiddlewareBindings()
    {
        $this->app->singleton(OAuthMiddleware::class, function ($app) {
            $httpHeadersOnly = config('oauth2.http_headers_only');

            return new OAuthMiddleware($app['oauth2-server.authorizer'], $httpHeadersOnly);
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
}
