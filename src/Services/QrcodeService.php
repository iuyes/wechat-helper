<?php namespace Huying\WechatHelper\Services;


class QrcodeService
{
    protected $base_service;

    public function __construct(BaseService $base_service)
    {
        $this->base_service = $base_service;
    }


    public function getQrcodeWithId($param, $dir, $filename = null, $expire = null)
    {
        return self::getQrcode('id', $param, $dir, $filename, $expire, $this->base_service->access_token);
    }

    public function getQrcodeWithStr($param,$dir, $filename=null)
    {
        return self::getQrcode('str', $param, $dir, $filename, null, $this->base_service->access_token);
    }

    /**
     * 获得微信服务器IP地址列表
     *
     * @return array
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public static function getWechatServerIp()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$this->base_service->access_token;
        $res = $this->base_service->wechatInterfaceGet($url);
        return $res['ip_list'];
    }

 

    protected static function getQrcodeByTicket($ticket, $dir, $filename = null)
    {
        $ticket = urlencode($ticket);
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
        /*$client = new Client();
        $history = new History();
        $client->getEmitter()->attach($history);*/
        $res = $this->base_service->wechatInterfaceGet($url, true);
        if (substr($dir, -1) != '/') {
            $dir = $dir.'/';
        }
        if ($filename === null) {
            $filename = md5($res->getBody()).'.jpg';
        }
        $file = fopen($dir.$filename, 'w');
        fwrite($file, $res->getBody());
        fclose($file);
        return true;
        /*if ($res->getStatusCode() == 200) {
            return $res;
        } else {
            throw new WechatInterfaceException('cannot get qrcode', 404, $history);
        }*/
    }

    protected static function getQrcode($type, $param, $dir, $filename = null, $expire = null, $access_token)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        if ($expire) {
            $data = array(
                'expire_seconds'    =>  $expire,
                'action_name'       =>  'QR_SCENE',
                'action_info'       =>  ['scene' => ['scene_'.$type => $param]],
                );
        } else {
            $data = array(
                'action_info'   =>  ['scene' => ['scene_'.$type => $param]],
                );
            if ($type == 'id') {
                $data['action_name'] = 'QR_LIMIT_SCENE';
            } elseif ($type = 'str') {
                $data['action_name'] = 'QR_LIMIT_STR_SCENE';
            }
        }
        $data = json_encode($data);
        $res = $this->base_service->wechatInterfacePost($url, $data);
        //return $res;
        return self::getQrcodeByTicket($res['ticket'], $dir, $filename);
    }

    

}
