<?php namespace Huying\WechatHelper\Services;

interface BasicSupportServiceInterface
{

    /**
     * 获取access_token
     *
     * @return string
     */
    public function getAccessToken();

    /**
     * 获得微信服务器IP地址列表
     *
     * @return array
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function getWechatServerIp();

}
