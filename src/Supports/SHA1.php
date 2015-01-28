<?php namespace Huying\WechatHelper\Supports;

use Exception;
use Huying\WechatHelper\Exceptions\MsgCryptException;

class SHA1
{
    /**
     * 用SHA1算法生成安全签名
     *
     * @param string $token 票据
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @param string $encryptMsg 密文消息
     * @return string
     * @throws \Huying\WechatHelper\Exceptions\MsgCryptException
     */
    public function getSHA1($token, $timestamp, $nonce, $encryptMsg)
    {
        //排序
        try {
            $array = array($encryptMsg, $token, $timestamp, $nonce);
            sort($array, SORT_STRING);
            $str = implode($array);
            return sha1($str);
        } catch (Exception $e) {
            throw new MsgCryptException(MsgCryptException::$computeSignatureError);
        }
    }
}
