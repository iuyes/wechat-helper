<?php namespace Huying\WechatHelper\Services;

use InvalidArgumentException;
use Config;
use App;

class CallbackService
{
	protected $params = 
	[
	'echostr'		=>	'',
	'signature'		=>	'',
	'timestamp'		=>	'',
	'nonce'			=>	'',
	'encrypt_type'	=>	'',
	'msg_signature'	=>	''
	];
	protected $token;
	protected $msgCrypt;
	protected $msg;


	public function __construct($token = null, MsgCryptService $msgCrypt = null, $options = [])
	{
		$this->token = $token ?: Config::get('test.token');
		$this->msgCrypt = $msgCrypt ?: new MsgCryptService($this->token, Config::get('test.aesKey'), Config::get('test.appId'));
		$this->getInputParams($options);
	}

	public function getMessage($post_str = null)
	{
		if ($this->checkSignature()) {
			$echostr = $this->params["echostr"];
			if ($echostr) {
				die($echostr);
			}
		} else {
			die('access denied');
		}

		if (App::environment('local')) {
			if (is_null($post_str)) {
				$post_str = file_get_contents("php://input");
			}
		} else {
			$post_str = file_get_contents("php://input");
		}

		
		$received_message = (array)simplexml_load_string($post_str, 'SimpleXMLElement', LIBXML_NOCDATA);
		$encrypt_type = $this->params["encrypt_type"];
		if (!$encrypt_type || $encrypt_type == 'raw') {
			$this->msg = $received_message;
			return $received_message;
		} elseif (isset($received_message['CreateTime'])) {//兼容模式
			$this->msg = $received_message;
			return $received_message;
		} else {
			$this->msg = $this->msgCrypt->decryptMsg($this->params["msg_signature"], $this->params["timestamp"], $this->params["nonce"], $post_str);
			return $this->msg;
		}
	}

	public function reply($message)
	{
		$reply_message['ToUserName'] = $this->msg['FromUserName'];
		$reply_message['FromUserName'] = $this->msg['ToUserName'];
		$reply_message['CreateTime'] = time();
		$reply_message = array_merge($reply_message, $message);
		$xml_message = self::xmlEncode($reply_message);
		$encrypt_type = $this->encrypt_type;

		if (isset($encrypt_type) && 'aes' == $encrypt_type) {
            $xml_message = $this->msgCrypt->encryptMsg($xml_message, time(), $this->nonce);
        }
		echo $xml_message;
		return true;
	}

	public function replyText($content)
    {
        $message = array(
            'MsgType'   =>  'text',
            'Content'   =>  $content
            );
        $this->reply($message);
    }

    public function replyVoice($mediaId)
    {
        $message = array(
            'MsgType'   =>  'voice',
            'Voice'   =>  ['MediaId' => $mediaId]
            );
        $this->reply($message);
    }

    public function replyVideo($mediaId, $title = null, $description = null) 
    { 
        $arr = ['MediaId' => $mediaId];
        $title && $arr['Title'] = $title;
        $description && $arr['Description'] = $description;
        $message = array( 
            'MsgType'   =>  'video', 
            'Video'     =>  $arr 
            );
        $this->reply($message); 
    }

    public function replyMusic($thumb_media_id, $music_url = null,
                               $hq_music_url = null, $title = null, $description = null)
    {
    	$music['ThumbMediaId'] = $thumb_media_id;
    	$music_url && $music['MusicUrl'] = $music_url;
    	$hq_music_url && $music['HQMusicUrl'] = $hq_music_url;
    	$title && $music['Title'] = $title;
    	$description && $music['Description'] = $description;
        $message = array(
            'MsgType'   =>  'music',
            'Music'   =>  $music
            );
        $this->reply($message);
    }

    public function replyNews($article_arr)
    { 
    	if (!is_array($article_arr) || count($article_arr) == 0) {
    		throw new InvalidArgumentException('图文消息参数有误');
    	}

    	$count = count($article_arr);
    	for($i = 0; $i < $count; $i ++) {
    		isset($article_arr[$i][0]) && $article[$i]['Title'] = $article_arr[$i][0];
    		isset($article_arr[$i][1]) && $article[$i]['Description'] = $article_arr[$i][1];
    		isset($article_arr[$i][2]) && $article[$i]['PicUrl'] = $article_arr[$i][2];
    		isset($article_arr[$i][3]) && $article[$i]['Url'] = $article_arr[$i][3];
    	}
    	$message = array(
    		'MsgType'		=>	'news',
    		'ArticleCount'	=>	count($article_arr),
    		'Articles'		=>	$article,
    		);
    	$this->reply($message);
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
            is_numeric($key) && $key = "item";
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






	protected function getInputParams($options = [])
    {
    	if (App::environment('local')) {
    		foreach ($this->params as $key => $value) {
    			if (isset($options[$key])) {
    				$this->params[$key] = $options[$key];
    			} elseif (isset($_GET[$key])) {
    				$this->params[$key] = $_GET[$key];
    			}
    		}
    	} else {
    		foreach ($this->params as $key => $value) {
    			$this->params[$key] = isset($_GET[$key]) ? $_GET[$key] : '';
    		}
    	}

    }

	protected function checkSignature()
    {
      $signature = $this->params["signature"];
      $timestamp = $this->params["timestamp"];
      $nonce = $this->params["nonce"];
      $token = $this->token;
      $tmpArr = array($token, $timestamp, $nonce);
      sort($tmpArr, SORT_STRING);
      $tmpStr = implode( $tmpArr );
      $tmpStr = sha1( $tmpStr );
      if ( $tmpStr == $signature ) {
             return true;
      } else {
             return false;
      }
    }
}