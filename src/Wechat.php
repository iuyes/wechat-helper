<?php namespace Huying\WechatHelper;

use Huying\WechatHelper\Services\CallbackInterface;
use Huying\WechatHelper\Services\GetMessageInterface;

class Wechat
{
	protected $callback;
	protected $getMessage;
	protected $token;
	protected $_msg;
	/*protected $access_token;
	protected $appid;
	protected $appsecret;*/

	public function __construct(CallbackInterface $callback, GetMessageInterface $getMessage,$token = null, $options = [])
	{
		$this->callback = $callback;
		$this->getMessage = $getMessage;
		$this->token = isset($token)?$token:Config::get('');
		/*$this->appid = isset($options["appid"])?$token:Config::get('');
		$this->appsecret = isset($options["appsecret"])?$token:Config::get('');*/
	}

	public function getMessage()
	{
		$this->_msg = $getMessage->getMessage($this->token);
		return $this->_msg;
	}

	public function reply($message)
	{
		return $callback->reply($message, $this->_msg['FromUserName'], $this->_msg['ToUserName']);
	}

	public function __get($name)
	{

	}

	public function __call()
	{
		
	}


}


$wechat->creatmenu($menu);
