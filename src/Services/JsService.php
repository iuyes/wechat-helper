<?php namespace Huying\WechatHelper\Services;

class JsService extends BaseService
{
	public function __construct($appid, $appsecret, $client, $history)
	{
		parent::__construct($appid, $appsecret, $client, $history);
	}

	public function getJsApiTicket()
	{
		$key = 'jsapi_ticket_'.$this->appid;
        if(Cache::has($key)) {
            return Cache::get($key);
        } else {
        	$access_token = self::getAccessToken($this->appid, $this->appsecret);
		    //var_dump($access_token);
        	$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token={$access_token}";
        	$res = self::wechatInterfaceGet($url);
        	$expire_mins = $res['expires_in']/60-1;
        	Cache::put($key, $res['ticket'], $expire_mins);
        	return $res['ticket'];
        }	
	}

	public function getJsApiInfo()
	{
		$jsapiTicket = $this->getJsApiTicket();
		$url = self::getUrl();
		$timestamp = time();
		$nonceStr = self::creatNonceStr();
		$string = "jsapi_ticket={$jsapiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
		$signature = sha1($string);
		$signPackage = array(
			"appId"     => $this->appid,
			"nonceStr"  => $nonceStr,
			"timestamp" => $timestamp,
			"url"       => $url,
			"signature" => $signature
			);
		return json_encode($signPackage);
	}

	public function getJsCardTicket()
	{
		$key = 'jscard_ticket_'.$this->appid;
        if(Cache::has($key)) {
            return Cache::get($key);
        } else {
        	$access_token = self::getAccessToken($this->appid, $this->appsecret);
		    //var_dump($access_token);
        	$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=wx_card&access_token={$access_token}";
        	$res = self::wechatInterfaceGet($url);
        	$expire_mins = $res['expires_in']/60-1;
        	Cache::put($key, $res['ticket'], $expire_mins);
        	return $res['ticket'];
        }	
	}

	public static function createNonceStr($length = 16)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	public static function getUrl()
    {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }
}