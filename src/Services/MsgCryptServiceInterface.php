<?php namespace Huying\WechatHelper\Services;

interface MsgCryptServiceInterface
{

    /**
     * 将公众平台回复用户的消息加密打包.
     *
     * 1.对要发送的消息进行AES-CBC加密
     * 2.生成安全签名
     * 3.将消息密文和安全签名打包成xml格式
     *
     * @param string $replyMsg 公众平台待回复用户的消息，xml格式的字符串
     * @param string $timestamp 时间戳，可以自己生成，也可以用URL参数的timestamp
     * @param string $nonce 随机串，可以自己生成，也可以用URL参数的nonce
     * @return string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串
     * @throws \Huying\WechatHelper\Exceptions\MsgCryptException
     */
    public function encryptMsg($replyMsg, $timestamp, $nonce);

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
     * @param &$msg string 解密后的原文，当return返回0时有效
     * @return string 解密后的原文
     * @throws \Huying\WechatHelper\Exceptions\MsgCryptException
     */
    public function decryptMsg($msgSignature, $timestamp=null, $nonce, $postData, $msg);
}
