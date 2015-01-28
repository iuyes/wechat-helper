<?php namespace Huying\WechatHelper\Supports;

use Exception;
use Huying\WechatHelper\Exceptions\MsgCryptException;

class Prpcrypt
{
    protected $appId;
    protected $key;
    protected $encoder;

    public function __construct($appId, $key, PKCS7Encoder $encoder=null)
    {
        $this->appId = $appId;
        $this->key = $key;
        if ($encoder === null) {
            $encoder = new PKCS7Encoder();
        }
        $this->encoder = $encoder;
    }

    /**
     * 对明文进行加密
     *
     * @param string $text 需要加密的明文
     * @return string 加密后的密文
     * @throws \Huying\WechatHelper\Exceptions\MsgCryptException
     */
    public function encrypt($text)
    {
        try {
            //获得16位随机字符串，填充到明文之前
            $random = $this->getRandomStr();
            $text = $random . pack("N", strlen($text)) . $text . $this->appId;
            // 网络字节序
            //TODO mcrypt_get_block_size有什么用？
            $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($this->key, 0, 16);
            //使用自定义的填充方式对明文进行补位填充
            $text = $this->encoder->encode($text);
            mcrypt_generic_init($module, $this->key, $iv);
            //加密
            $encrypted = mcrypt_generic($module, $text);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
            //使用BASE64对加密后的字符串进行编码
            return base64_encode($encrypted);
        } catch (Exception $e) {
            throw new MsgCryptException(MsgCryptException::$encryptAESError);
        }
    }

    /**
     * 对密文进行解密
     *
     * @param string $encrypted 需要解密的密文
     * @return string 解密得到的明文
     * @throws \Huying\WechatHelper\Exceptions\MsgCryptException
     */
    public function decrypt($encrypted)
    {

        try {
            //使用BASE64对需要解密的字符串进行解码
            $ciphertextDec = base64_decode($encrypted);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($this->key, 0, 16);
            mcrypt_generic_init($module, $this->key, $iv);

            //解密
            $decrypted = mdecrypt_generic($module, $ciphertextDec);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        } catch (Exception $e) {
            throw new MsgCryptException(MsgCryptException::$decryptAESError);
        }

        try {
            //去除补位字符
            $result = $this->encoder->decode($decrypted);
            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16) {
                return '';
            }
            $content = substr($result, 16, strlen($result));
            $lenList = unpack("N", substr($content, 0, 4));
            $xmlLen = $lenList[1];
            $xmlContent = substr($content, 4, $xmlLen);
            $fromAppId = substr($content, $xmlLen + 4);
        } catch (Exception $e) {
            throw new MsgCryptException(MsgCryptException::$illegalBuffer);
        }
        if ($fromAppId != $this->appId) {
            throw new MsgCryptException(MsgCryptException::$validateAppidError);
        }
        return $xmlContent;
    }

    /**
     * 随机生成16位字符串
     *
     * @return string 生成的字符串
     */
    protected function getRandomStr()
    {
        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }
}