<?php namespace Huying\WechatHelper\Supports;

use DOMDocument;
use Exception;
use Huying\WechatHelper\Exceptions\MsgCryptException;

class XMLParser
{
    protected $document;

    public function __construct(DOMDocument $document=null)
    {
        if ($document === null) {
            $document = new DOMDocument();
        }
        $this->document = $document;
    }

    /**
     * 提取出xml数据包中的加密消息
     *
     * @param string $xmlText 待提取的xml字符串
     * @return string 提取出的加密消息字符串
     * @throws \Huying\WechatHelper\Exceptions\MsgCryptException
     */
    public function extract($xmlText)
    {
        try {
            $this->document->loadXML($xmlText);
            $arrayE = $this->document->getElementsByTagName('Encrypt');
            $encrypt = $arrayE->item(0)->nodeValue;
            return $encrypt;
        } catch (Exception $e) {
            throw new MsgCryptException(MsgCryptException::$parseXmlError);
        }
    }

    /**
     * 生成xml消息
     *
     * @param string $encrypt 加密后的消息密文
     * @param string $signature 安全签名
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @return string
     */
    public function generate($encrypt, $signature, $timestamp, $nonce)
    {
        $format = "<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }
}

