<?php namespace Huying\WechatHelper\Services;


class MenuService
{
    protected $access_token;
    public function __construct($access_token = null)
    {
        if ($access_token) {
            $this->access_token = $access_token;   
        } else {
            $this->access_token = BasicService::getAccessToken();
        }
    }

    public function createMenu($json_menu)
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->access_token;
    	BasicService::wechatInterfacePost($url, $json_menu);
    	return true;
    }

    public function getMenu()
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$this->access_token;
    	$res = BasicService::wechatInterfaceGet($url);
    	return $res;
    }

    public function delMenu()
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$this->access_token;
    	BasicService::wechatInterfaceGet($url);
    	return true;
    }


}