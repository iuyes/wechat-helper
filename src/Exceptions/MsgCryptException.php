<?php namespace Huying\WechatHelper\Exceptions;

use Exception;

class MsgCryptException extends Exception
{
    public static $oK = 0;
    public static $validateSignatureError = -40001;
    public static $parseXmlError = -40002;
    public static $computeSignatureError = -40003;
    public static $illegalAesKey = -40004;
    public static $validateAppidError = -40005;
    public static $encryptAESError = -40006;
    public static $decryptAESError = -40007;
    public static $illegalBuffer = -40008;
    public static $encodeBase64Error = -40009;
    public static $decodeBase64Error = -40010;
    public static $genReturnXmlError = -40011;

    protected static $errorCode = [
        '0' => 'OK',
        '-40001' => '签名验证错误',
        '-40002' => 'xml解析失败',
        '-40003' => 'sha加密生成签名失败',
        '-40004' => 'encodingAesKey 非法',
        '-40005' => 'appid 校验错误',
        '-40006' => 'aes 加密失败',
        '-40007' => 'aes 解密失败',
        '-40008' => '解密后得到的buffer非法',
        '-40009' => 'base64加密失败',
        '-40010' => 'base64解密失败',
        '-40011' => '生成xml失败'
    ];

    protected $notDefinedMessage = '未定义错误';

    public function __construct($code, Exception $previous = null)
    {
        if (isset(self::$errorCode[$code])) {
            $message = self::$errorCode[$code];
        } else {
            $message = $this->notDefinedMessage;
        }
        parent::__construct($message, $code, $previous);
    }
}
