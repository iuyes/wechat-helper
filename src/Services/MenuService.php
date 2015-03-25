<?php namespace Huying\WechatHelper\Services;


class MenuService
{
    protected $base_service;

    public function __construct(BaseService $base_service)
    {
        $this->base_service = $base_service;
    }


    public function createMenu($json_menu)
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->base_service->access_token;
    	$this->base_service->wechatInterfacePost($url, $json_menu);
    	return true;
    }

    public function getMenu()
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$this->base_service->access_token;
    	$res = $this->base_service->wechatInterfaceGet($url);
    	return $res;
    }

    public function delMenu()
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$this->base_service->access_token;
    	$this->base_service->wechatInterfaceGet($url);
    	return true;
    }


}