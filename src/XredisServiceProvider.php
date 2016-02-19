<?php namespace Xredis;

use Illuminate\Support\ServiceProvider;

class XredisServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Jredis', function ($app) {
            return new JDatabase($app['config']['database.redis']);
        });
        
        $this->app->singleton('Sredis', function ($app) {
            return new SDatabase($app['config']['database.redis']);
        });
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Jredis', 'Sredis'];
    }
}
