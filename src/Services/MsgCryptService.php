<?php namespace Huying\WechatHelper\Services;

use Huying\WechatHelper\Supports\Prpcrypt;
use Huying\WechatHelper\Supports\SHA1;
use Huying\WechatHelper\Supports\XMLParser;
use Huying\WechatHelper\Exceptions\MsgCryptException;

class MsgCryptService
{
    protected $token;
    protected $aesKey;
    protected $appId;
    protected $prpcrypt;
    protected $sha1;
    protected $XMLParser;

    function __construct($token = null, $aesKey = null, $appId = null, Prpcrypt $prpcrypt=null, SHA1 $sha1=null, XMLParser $XMLParser=null)
    {
        $this->token = isset($token) ? : Config::get('token');
        $this->aesKey = isset($aesKey) ? : Config::get('aesKey');
        $this->appId = isset($appId) ? : Config::get('appId');
        if ($prpcrypt === null) {
            $prpcrypt = new Prpcrypt($this->appId, $this->aesKey);
        }
        $this->prpcrypt = $prpcrypt;
        if ($sha1 === null) {
            $sha1 = new SHA1();
        }
        $this->sha1 = $sha1;
        if ($XMLParser === null) {
            $XMLParser = new XMLParser();
        }
        $this->XMLParser = $XMLParser;
    }

    /**
     * 将公众平台回复用户的消息加密打包.
     *
     * 1.对要发送的消息进行AES-CBC加密
     * 2.生成安全签名
     * 3.将消息密文和安全签名打包成xml格式
     *
     * @param string $replyMsg 公众平台待回复用户的消息，xml格式的字符串
     * @param string $timestamp = null 时间戳，可以自己生成，也可以用URL参数的timestamp
     * @param string $nonce 随机串，可以自己生成，也可以用URL参数的nonce
     * @return string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串
     * @throws \Huying\WechatHelper\Exceptions\MsgCryptException
     */
    public function encryptMsg($replyMsg, $timestamp=null, $nonce)
    {
        //加密
        $encrypt = $this->prpcrypt->encrypt($replyMsg);

        if ($timestamp == null) {
            $timestamp = time();
        }

        //生成安全签名
        $signature = $this->sha1->getSHA1($this->token, $timestamp, $nonce, $encrypt);

        //生成发送的xml
        $encryptMsg = $this->XMLParser->generate($encrypt, $signature, $timestamp, $nonce);
        return $encryptMsg;
    }

    /**
     * 检验消息的真实性，并且获取解密后的明文.
     *
     * 1.利用收到的密文生成安全签名，进行签名验证
     * 2.若验证通过，则提取xml中的加密消息
     * 3.对消息进行解密
     *
     * @param string $msgSignature 签名串，对应URL参数的msg_signature
     * @param string $timestamp 时间戳，对应URL参数的timestamp
     * @param string $nonce 随机串，对应URL参数的nonce
     * @param string $postData 密文，对应POST请求的数据
     * @return string 解密后的原文
     * @throws \Huying\WechatHelper\Exceptions\MsgCryptException
     */
    public function decryptMsg($msgSignature, $timestamp = null, $nonce, $postData)
    {
        if (strlen($this->aesKey) != 43) {
            throw new MsgCryptException(MsgCryptException::$illegalAesKey);
        }
        //提取密文
        $encrypt = $this->XMLParser->extract($postData);
        if ($timestamp == null) {
            $timestamp = time();
        }

        //验证安全签名
        $signature = $this->sha1->getSHA1($this->token, $timestamp, $nonce, $encrypt);
        if ($signature != $msgSignature) {
            throw new MsgCryptException(MsgCryptException::$validateSignatureError);
        }

        $msg = $this->prpcrypt->decrypt($encrypt, $this->appId);
        return $msg;
    }
}
