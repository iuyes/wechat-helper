<?php namespace Huying\WechatHelper;

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
//use Huying\WechatHelper\Services\MsgCryptService;



class Wechat
{
	protected $classes = array (
		'base'		=>	'',
		'callback'	=>	'',
		'customer'	=>	'',
		'js'		=>	'',
		'media'		=>	'',
		'user'		=>	'',
		'menu'		=>	'',
		'qrcode'	=>	'',
		'send'		=>	'',
		);
	protected $client;
	protected $history;
	
	

	public function __construct(
		BaseService $base = null, CallbackService $callback = null,
	    CustomerService $customer = null, JsService $js = null, 
		MediaService $media = null, UserService $user = null,
		MenuService $menu = null, QrcodeService $qrcode = null, SendService $send = null
    )
	{
		$this->client = new Client();
		$this->history = new History();
		$this->client->getEmitter()->attach($this->history);
		/*foreach ($this->classes as $key => $value) {
			$class_name = 'Huying\WechatHelper\Services\\'.ucfirst($key).'Service';
			if ($key != 'callback') {
				$this->classes[$key] = $$key ?: new $class_name(Config::get('test.appId'), Config::get('test.appsecret'), $this->client, $this->history);
			} else {
				$this->classes['callback'] = $callback ?: new CallbackService();
			}
		}*/
		$this->classes['base'] = $base ?: new BaseService(Config::get('test.appId'), Config::get('test.appsecret'), $this->client, $this->history);
		$this->classes['callback'] = $callback ?: new CallbackService();
		//$this->classes['member'] = $member ?: new MemberService($this->classes['base']);
		foreach ($this->classes as $key => $value) {
			$class_name = 'Huying\WechatHelper\Services\\'.ucfirst($key).'Service';
			if ($key != 'callback' && $key != 'base') {
				$this->classes[$key] = $$key ?: new $class_name($this->classes['base']);
			}
	}

	public function __call($method, $args)
	{
		foreach ($this->classes as $key => $value) {
			if (method_exists($value, $method)) {
				return call_user_func_array(array($value, $method), $args);
			}
		}
	}


}
