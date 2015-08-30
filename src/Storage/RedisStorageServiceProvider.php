<?php
/**
 * Fluent Storage Service Provider for the OAuth 2.0 Server
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Storage;

use Illuminate\Support\ServiceProvider;
use Fahmiardi\OAuth2\Server\Storage\Util\RedisCapsule;
use Fahmiardi\OAuth2\Server\Storage\Redis\RedisAccessToken;
use Fahmiardi\OAuth2\Server\Storage\Redis\RedisClient;
use Fahmiardi\OAuth2\Server\Storage\Redis\RedisScope;
use Fahmiardi\OAuth2\Server\Storage\Redis\RedisSession;
use League\OAuth2\Server\Storage\AccessTokenInterface;
use League\OAuth2\Server\Storage\ClientInterface;
use League\OAuth2\Server\Storage\ScopeInterface;
use League\OAuth2\Server\Storage\SessionInterface;

class RedisStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerStorageBindings();
        $this->registerInterfaceBindings();
    }

    /**
     * Bind the storage implementations to the IoC container
     * @return void
     */
    public function registerStorageBindings()
    {
        $redis = new RedisCapsule(config('database.oauth2-redis'));
        $redis->setAsGlobal();

        $this->app->singleton(RedisAccessToken::class, function () {
            return new RedisAccessToken();
        });

        $this->app->singleton(RedisClient::class, function () {
            return new RedisClient();
        });

        $this->app->singleton(RedisScope::class, function () {
            return new RedisScope();
        });

        $this->app->singleton(RedisSession::class, function () {
            return new RedisSession();
        });
    }

    /**
     * Bind the interfaces to their implementations
     * @return void
     */
    public function registerInterfaceBindings()
    {
        $this->app->bind(ClientInterface::class, RedisClient::class);
        $this->app->bind(ScopeInterface::class, RedisScope::class);
        $this->app->bind(SessionInterface::class, RedisSession::class);
        $this->app->bind(AccessTokenInterface::class, RedisAccessToken::class);
    }
}

