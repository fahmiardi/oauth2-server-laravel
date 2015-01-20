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

use Illuminate\Contracts\Exception\Handler;

class RedisStorageServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

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
        $redis = new RedisCapsule(array(
            'default' => array(
                'host'      => '127.0.0.1',
                'port'      => 6379,
                'scheme'    => 'tcp',
            )
        ));
        $redis->setAsGlobal();

        $this->app->bindShared('Fahmiardi\OAuth2\Server\Storage\Redis\RedisAccessToken', function () {
            return new RedisAccessToken();
        });

        $this->app->bindShared('Fahmiardi\OAuth2\Server\Storage\Redis\RedisClient', function () {
            return new RedisClient();
        });

        $this->app->bindShared('Fahmiardi\OAuth2\Server\Storage\Redis\RedisScope', function () {
            return new RedisScope();
        });

        $this->app->bindShared('Fahmiardi\OAuth2\Server\Storage\Redis\RedisSession', function () {
            return new RedisSession();
        });
    }

    /**
     * Bind the interfaces to their implementations
     * @return void
     */
    public function registerInterfaceBindings()
    {
        $this->app->bind('League\OAuth2\Server\Storage\ClientInterface',       'Fahmiardi\OAuth2\Server\Storage\Redis\RedisClient');
        $this->app->bind('League\OAuth2\Server\Storage\ScopeInterface',        'Fahmiardi\OAuth2\Server\Storage\Redis\RedisScope');
        $this->app->bind('League\OAuth2\Server\Storage\SessionInterface',      'Fahmiardi\OAuth2\Server\Storage\Redis\RedisSession');
        $this->app->bind('League\OAuth2\Server\Storage\AccessTokenInterface',  'Fahmiardi\OAuth2\Server\Storage\Redis\RedisAccessToken');
    }
}
 