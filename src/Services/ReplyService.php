<?php namespace Huying\WechatHelper\Services;

class ReplyService
{
	protected $msgCrypt;
	protected $nonce;

	public function __construct($nonce = null, MsgCryptService $msgCrypt = null)
	{
		$this->msgCrypt = isset($msgCrypt) ?: new MsgCryptService();
		$this->nonce = $nonce;
	}

	public function reply($message, $touser, $fromuser, $encrypt_type = null)
	{
		$reply_message['ToUserName'] = $touser;
		$reply_message['FromUserName'] = $fromuser;
		$reply_message['CreateTime'] = time();
		$reply_message = array_merge($reply_message, $message);
		$xml_message = self::xmlEncode($reply_message);


		if (isset($encrypt_type) && 'aes' == $encrypt_type) {
            $xml_message = $this->msgCrypt->encryptMsg($xml_message, time(), $this->nonce);
        }
		echo $xml_message;
		return true;
	}

    public function replyText($content, $touser, $fromuser, $encrypt_type = null)
    {
        $message = array(
            'MsgType'   =>  'text',
            'Content'   =>  $content
            );
        $this->reply($message, $touser, $fromuser, $encrypt_type);
    }

    public function replyVoice($mediaId, $touser, $fromuser, $encrypt_type = null)
    {
        $message = array(
            'MsgType'   =>  'voice',
            'Voice'   =>  ['MediaId' => $mediaId]
            );
        $this->reply($message, $touser, $fromuser, $encrypt_type);
    }

    public function replyVideo($mediaId, $touser, $fromuser, $title = null, $description = null, $encrypt_type = null) 
    { 
        $arr = ['MediaId' => $mediaId];
        $title && $arr['Title'] = $title;
        $description && $arr['Description'] = $description;
        $message = array( 
            'MsgType'   =>  'video', 
            'Video'     =>  $arr 
            );\c bbbbbbbbbbbbbbbbbbbbbbbbbbbb        $this->reply($message, $touser, $fromuser, $encrypt_type); 
    }

    public function replyMusic($content, $touser, $fromuser, $encrypt_type = null)
    {
        $message = array(
            'MsgType'   =>  'text',
            'Content'   =>  $content
            );
        $this->reply($message, $touser, $fromuser, $encrypt_type);
    }



	public static function xmlSafeStr($str)
    {
        return '<![CDATA['.preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$str).']]>';
    }

    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string
     */
    public static function dataToXml($data)
    {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            if ($key === 'CreateTime') {
            	$xml .= "<$key>$val</$key>";
            } else {
            	$xml .= "<$key>";
            	$xml .= (is_array($val) || is_object($val)) ? self::data_to_xml($val) : self::xmlSafeStr($val);
            	list($key,) = explode(' ', $key);
            	$xml .= "</$key>";
            }
            
        }
        return $xml;
    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id 数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public static function xmlEncode($data, $root = 'xml', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8')
    {
        if (is_array($attr)) {
            $attrArray = array();
            foreach ($attr as $key => $value) {
                $attrArray[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $attrArray);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<{$root}{$attr}>";
        $xml .= self::dataToXml($data, $item, $id);
        $xml .= "</{$root}>";
        return $xml;
    }

}