<?php namespace Huying\WechatHelper;

use Illuminate\Support\ServiceProvider;

class WechatHelperServiceProvider extends ServiceProvider
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


        //
    }

    public function boot()
    {
        $this->package('huying/wechat-helper', null, __DIR__);
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
