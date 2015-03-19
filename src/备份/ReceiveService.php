<?php namespace Huying\WechatHelper\Services;

use Config;
use App;


class ReceiveService
{

	
	protected $params = 
	[
	'echostr'		=>	'',
	'signature'		=>	'',
	'timestamp'		=>	'',
	'nonce'			=>	'',
	'encrypt_type'	=>	'',
	'msg_signature'	=>	''
	];
	protected $token;
	protected $msgCrypt;


	public function __construct(MsgCryptService $msgCrypt =null, $token = null, $options = [])
	{
		$this->token = isset($token) ? $token : Config::get('');
		$this->msgCrypt = isset($msgCrypt) ? $msgCrypt : new MsgCryptService();
		$this->getInputParams($options);
		$this->msgCrypt = $msgCrypt;
	}	


	public function getMessage($post_str = null)
	{
		if ($this->checkSignature()) {
			$echostr = $this->params["echostr"];
			if ($echostr) {
				die($echostr);
			}
		} else {
			die('access denied');
		}

		if (App::environment('local')) {
			if (is_null($post_str)) {
				$post_str = file_get_contents("php://input");
			}
		} else {
			$post_str = file_get_contents("php://input");
		}

		
		$received_message = (array)simplexml_load_string($post_str, 'SimpleXMLElement', LIBXML_NOCDATA);
		$encrypt_type = $this->params["encrypt_type"];
		if (!$encrypt_type || $encrypt_type == 'raw') {
			return $received_message;
		} elseif (isset($received_message['CreateTime'])) {//兼容模式
			return $received_message;
		} else {
			return $this->msgCrypt->decryptMsg($this->params["msg_signature"], $this->params["timestamp"], $this->params["nonce"], $post_str);
		}



	}

	protected function getInputParams($options = [])
    {
    	if (App::environment('local')) {
    		foreach ($this->params as $key => $value) {
    			if (isset($options[$key])) {
    				$this->params[$key] = $options[$key];
    			} elseif (isset($_GET[$key])) {
    				$this->params[$key] = $_GET[$key];
    			}
    		}
    	} else {
    		foreach ($this->params as $key => $value) {
    			$this->params[$key] = isset($_GET[$key]) ? $_GET[$key] : '';
    		}
    	}

    }

	protected function checkSignature()
    {
      $signature = $this->params["signature"];
      $timestamp = $this->params["timestamp"];
      $nonce = $this->params["nonce"];
      $token = $this->token;
      $tmpArr = array($token, $timestamp, $nonce);
      sort($tmpArr, SORT_STRING);
      $tmpStr = implode( $tmpArr );
      $tmpStr = sha1( $tmpStr );
      if( $tmpStr == $signature ){
             return true;
      }else{
             return false;
      }
    }

    
}