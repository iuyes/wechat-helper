<?php namespace Huying\WechatHelper\Services;


use App;

class test
{

	 function hello($a,$b=1,$c){
                return $a+$b+$c;
        }
	public function index()
	{
		//return \Config::get("huying/wechat-helper::config.token");
		//return $this->app['config']['wechat-helper::config.token'];
		//return $this->hello(1,1,1);
		//$a = 10;
		//$c = '';
		//($b = $c) || ($b = $a);
		//return $b;
		//$a = $_GET['a'];
		//var_dump(isset($a['2']));
		/*if ('') {
			echo 'yes';
		} else {
			echo 'no';
		}*/
		/*$s = ['a'=>'b'];
		echo current($key);
		return App::environment();*/
		$aesKey = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG";
		$token = "pamtest";
		$timeStamp = "1409304348";
		$nonce = "xxxxxx";
		$appId = "wxb11529c136998cb6";
		$text = "<xml><ToUserName><![CDATA[oia2Tj我是中文jewbmiOUlr6X-1crbLOvLw]]></ToUserName><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>1407743423</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>";

		$msgcrypt = new MsgCryptService($token, $aesKey, $appId);
		$encryptMsg = $msgcrypt->encryptMsg($text, $timeStamp, $nonce);
		print_r($encryptMsg);

		$xml_tree = new \DOMDocument();
		$xml_tree->loadXML($encryptMsg);
		$array_e = $xml_tree->getElementsByTagName('Encrypt');
		$array_s = $xml_tree->getElementsByTagName('MsgSignature');
		$encrypt = $array_e->item(0)->nodeValue;
		$msg_sign = $array_s->item(0)->nodeValue;

		$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
		$from_xml = sprintf($format, $encrypt);

		//print_r($msgcrypt->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml));


	$params = 
	[
	'echostr'		=>	'',
	'signature'		=>	$msg_sign,
	'timestamp'		=>	$timeStamp,
	'nonce'			=>	$nonce,
	'encrypt_type'	=>	'aes',
	'msg_signature'	=>	$msg_sign
	];
	  $tmpArr = array($token, $timeStamp, $nonce);
      sort($tmpArr, SORT_STRING);
      $tmpStr = implode( $tmpArr );
      $params['signature'] = sha1( $tmpStr );


      $message = new GetMessageFromWechat($msgcrypt, $token, $params);
      print_r($message->getMessage($from_xml));
      $err_aeskey = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFg";
      $new_msgcrypt = new MsgCryptService($token, $err_aesKey, $appId);
      $message = new GetMessageFromWechat($new_msgcrypt, $token, $params);
      print_r($message->getMessage($from_xml));

      $reply = new CallbackToWechat($msgcrypt, $nonce);
      $reply->reply([], 1, 2);

		return 22222;



	}
}