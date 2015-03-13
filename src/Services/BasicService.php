<?php namespace Huying\WechatHelper\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\History;
use Huying\WechatHelper\Exceptions\WechatInterfaceException;

class BasicService
{
    public function __construct()
    {
    }

    /**
     * 获取access_token
     *
     * @return string
     */
    public static function getAccessToken($appid = null, $appsecret = null)
    {
        $appid = isset($appid)?:Config;
        $appsecret = isset($appsecret)?:Config;
        $key = 'access_token_'.$appid;
        if(Cache::has($key)) {
            return Cache::get($key);
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
            $res = self::guzzleGet($url);
            $expire_mins = $res['expires_in']/60-1;
            Cache::put($key, $res['access_token'], $expire_mins);
            return $res['access_token'];
        }
        
    }

    /**
     * 获得微信服务器IP地址列表
     *
     * @return array
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public static function getWechatServerIp($appid = null, $appsecret = null)
    {
        $appid = isset($appid)?:Config;
        $appsecret = isset($appsecret)?:Config;
        $access_token = self::getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$access_token;
        $res = self::guzzleGet($url);
        return $res['ip_list'];
    }

    public static function wechatInterfaceGet($url)
    {
        $client = new Client();
        $history = new History();
        $client->getEmitter()->attach($history);
        $res = $client->get($url)->json();
        if (isset($res['errcode']) && $res['errcode']) {
            throw new WechatInterfaceException($res['errmsg'], $res['errcode'], $history);
        }
        return $res;

    }

    public static function wechatInterfacePost($url, $data)
    {
        $client = new Client();
        $history = new History();
        $client->getEmitter()->attach($history);
        $res = $client->post($url, ['body' => $data])->json();
        if (isset($res['errcode'])  && $res['errcode']) {
            throw new WechatInterfaceException($res['errmsg'], $res['errcode'], $history);
        }
        return $res;

    }

    public static function getQrcodeByTicket($ticket)
    {
        $ticket = urlencode($ticket);
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
        $client = new Client();
        $history = new History();
        $client->getEmitter()->attach($history);
        $res = $client->get($url);
        if ($res->getStatusCode() == 200) {
            return $res;
        } else {
            throw new WechatInterfaceException('cannot get qrcode', 404, $history);
        }
    }

    public static function getQrcode($type, $param, $expire = null, $access_token = null)
    {
        if (!$access_token) {
            $access_token = self::getAccessToken();
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        if ($expire) {
            $data = arrary(
                'expire_seconds'    =>  $expire,
                'action_name'       =>  'QR_SCENCE',
                'action_info'       =>  ['scence' => ['scence_'.$type => $param]],
                );
        } else {
            $data = array(
                'action_info'   =>  ['scence' => ['scence_'.$type => $param]],
                );
            if ($type == 'id') {
                $data['action_name'] = 'QR_LIMIT_SCENCE';
            } elseif ($type = 'str') {
                $data['action_name'] = 'QR_LIMIT_STR_SCENCE';
            }
        }
        $data = json_encode($data);
        $res = self::wechatInterfacePost($url, $data);
        return self::getQrcodeByTicket($res['ticket']);
    }

    public static function getQrcodeWithId($param, $expire = null, $access_token = null)
    {
        return self::getQrcode('id', $param, $expire, $access_token);
    }

    public static function getQrcodeWithStr($param, $access_token = null)
    {
        return self::getQrcode('str', $param, null, $access_token);
    }

}
