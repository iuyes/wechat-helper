<?php namespace Huying\WechatHelper\Services;


class MenuService extends BaseService
{
    public function __construct($appid, $appsecret, $client, $history)
    {
        parent::__construct($appid, $appsecret, $client, $history);
    }

    public function createMenu($json_menu)
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->access_token;
    	self::wechatInterfacePost($url, $json_menu);
    	return true;
    }

    public function getMenu()
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$this->access_token;
    	$res = self::wechatInterfaceGet($url);
    	return $res;
    }

    public function delMenu()
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$this->access_token;
    	self::wechatInterfaceGet($url);
    	return true;
    }


}