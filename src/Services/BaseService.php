<?php namespace Huying\WechatHelper\Services;



use Huying\WechatHelper\Exceptions\WechatInterfaceException;
use Config;
use Cache;

class BaseService
{
	protected $appid;
	protected $appsecret;
	public $access_token;
	protected static $client;
	protected static $history;


	public function __construct($appid, $appsecret, $client, $history)
	{
		$this->appid = $appid;
		$this->appsecret = $appsecret;
		self::$client = $client;
		self::$history = $history;
		$this->access_token = self::getAccessToken($appid, $appsecret);

	}

	/**
     * 获取access_token
     *
     * @return string
     */
    public static function getAccessToken($appid, $appsecret, $force = 0)
    {
    	$key = 'access_token_'.$appid;
    	if ($force) {
    		Cache::forget($key);
    	}
        if(Cache::has($key)) {
        	return Cache::get($key);
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
        $res = self::wechatInterfaceGet($url);
        $expire_mins = $res['expires_in']/60-1;
        Cache::put($key, $res['access_token'], $expire_mins);
        return $res['access_token'];
        
    }

    public static function wechatInterfaceGet($url, $raw = false)
    {
        $client = self::$client;
        $history = self::$history;
        if (!$raw) {
            $res = json_decode(urldecode($client->get($url)->getBody()), true);
            if (isset($res['errcode']) && $res['errcode']) {
                throw new WechatInterfaceException($res['errmsg'].' '.$res['errcode'], $res['errcode'], $history);
            }
        } else {
            $res = $client->get($url)->getBody();
        }
        return $res;

    }

    public static function wechatInterfacePost($url, $data, $raw = false)
    {
        $client = self::$client;
        $history = self::$history;
        //$client->getEmitter()->attach($history);
        if (!raw) {
        	$res = json_decode(urldecode($client->post($url, ['body' => $data])->getBody()), true);
        	if (isset($res['errcode'])  && $res['errcode']) {
        		throw new WechatInterfaceException($res['errmsg'].' '.$res['errcode'], $res['errcode'], $history);
        	}
        } else {
        	$res = $client->post($url, ['body' => $data])->getBody();
        }
        return $res;
    }
}