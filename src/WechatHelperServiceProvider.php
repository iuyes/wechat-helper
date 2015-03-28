<?php namespace Huying\WechatHelper;

use Illuminate\Support\ServiceProvider;
use Config;
use Huying\WechatHelper\Services\MsgCryptService;
use Huying\WechatHelper\Services\BaseService;
use Huying\WechatHelper\Services\CallbackService;
use Huying\WechatHelper\Services\CustomerService;
use Huying\WechatHelper\Services\JsService;
use Huying\WechatHelper\Services\MediaService;
use Huying\WechatHelper\Services\UserService;
use Huying\WechatHelper\Services\MenuService;
use Huying\WechatHelper\Services\QrcodeService;
use Huying\WechatHelper\Services\SendService;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\History;
use Config;

class WechatHelperServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    protected $client;
    protected $history;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->client = new Client();
        $this->history = new History();
        $this->app['wechat-helper.msgcrypt'] = $this->app->share(function($app) 
            {return new MsgCryptService(Config::get('test.token'), Config::get('test.aesKey'), Config::get('test.appId'));});
        $this->app['wechat-helper.callback'] = $this->app->share(function($app)
            {return new CallbackService(Config::get('test.token'), $app['wechat-helper.msgcrypt']);});
        $this->app['wechat-helper.base'] = $this->app->share(function($app)
            {return new BaseService(Config::get('test.appId'), Config::get('test.appsecret'), $this->client, $this->history);});
        $this->app['wechat-helper.user'] = $this->app->share(function($app)
            {return new UserService($app['wechat-helper.base']);});
        $this->app['wechat-helper.js'] = $this->app->share(function($app)
            {return new JsService($app['wechat-helper.base']);});
        $this->app['wechat-helper.media'] = $this->app->share(function($app)
            {return new MediaService($app['wechat-helper.base']);});
        $this->app['wechat-helper.menu'] = $this->app->share(function($app)
            {return new MenuService($app['wechat-helper.base']);});
        $this->app['wechat-helper.qrcode'] = $this->app->share(function($app)
            {return new QrcodeService($app['wechat-helper.base']);});
        $this->app['wechat-helper.send'] = $this->app->share(function($app)
            {return new SendService($app['wechat-helper.base']);});


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
