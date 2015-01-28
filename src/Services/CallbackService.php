<?php namespace Huying\WechatHelper\Services;

use RuntimeException;
USE InvalidArgumentException;
use Huying\WechatHelper\Exceptions\XMLParseErrorException;
use Huying\WechatHelper\Exceptions\SignatureWrongException;
use Huying\WechatHelper\Exceptions\ReplyNotFoundException;

class CallbackService implements CallbackServiceInterface
{
    const MSGTYPE_TEXT = 'text';
    const MSGTYPE_IMAGE = 'image';
    const MSGTYPE_LOCATION = 'location';
    const MSGTYPE_LINK = 'link';
    const MSGTYPE_EVENT = 'event';
    const MSGTYPE_MUSIC = 'music';
    const MSGTYPE_NEWS = 'news';
    const MSGTYPE_VOICE = 'voice';
    const MSGTYPE_VIDEO = 'video';
    const EVENT_SUBSCRIBE = 'subscribe';       //订阅
    const EVENT_UNSUBSCRIBE = 'unsubscribe';   //取消订阅
    const EVENT_SCAN = 'SCAN';                 //扫描带参数二维码
    const EVENT_LOCATION = 'LOCATION';         //上报地理位置
    const EVENT_MENU_VIEW = 'VIEW';                     //菜单 - 点击菜单跳转链接
    const EVENT_MENU_CLICK = 'CLICK';                   //菜单 - 点击菜单拉取消息
    const EVENT_MENU_SCAN_PUSH = 'scancode_push';       //菜单 - 扫码推事件(客户端跳URL)
    const EVENT_MENU_SCAN_WAITMSG = 'scancode_waitmsg'; //菜单 - 扫码推事件(客户端不跳URL)
    const EVENT_MENU_PIC_SYS = 'pic_sysphoto';          //菜单 - 弹出系统拍照发图
    const EVENT_MENU_PIC_PHOTO = 'pic_photo_or_album';  //菜单 - 弹出拍照或者相册发图
    const EVENT_MENU_PIC_WEIXIN = 'pic_weixin';         //菜单 - 弹出微信相册发图器
    const EVENT_MENU_LOCATION = 'location_select';      //菜单 - 弹出地理位置选择器
    const EVENT_SEND_MASS = 'MASSSENDJOBFINISH';        //发送结果 - 高级群发完成
    const EVENT_SEND_TEMPLATE = 'TEMPLATESENDJOBFINISH';//发送结果 - 模板消息发送结果
    const EVENT_KF_SEESION_CREATE = 'kfcreatesession';  //多客服 - 接入会话
    const EVENT_KF_SEESION_CLOSE = 'kfclosesession';    //多客服 - 关闭会话
    const EVENT_KF_SEESION_SWITCH = 'kfswitchsession';  //多客服 - 转接会话
    const EVENT_CARD_PASS = 'card_pass_check';          //卡券 - 审核通过
    const EVENT_CARD_NOTPASS = 'card_not_pass_check';   //卡券 - 审核未通过
    const EVENT_CARD_USER_GET = 'user_get_card';        //卡券 - 用户领取卡券
    const EVENT_CARD_USER_DEL = 'user_del_card';        //卡券 - 用户删除卡券
    protected $rawReceivedMessage;
    protected $receivedMessage;
    protected $replyMessage = null;
    protected $token;
    protected $timestamp;
    protected $nonce;
    protected $signature;
    protected $appId;
    protected $aesKey;
    protected $originId;
    protected $isSecure;
    protected $msgCrypt;
    protected $msgSignature;

    public function __construct($receivedMessage, $token, $timestamp, $nonce, $signature, $appId=null, $aesKey=null, $originId=null, $isSecure=false, MsgCryptService $msgCrypt=null, $msgSignature)
    {
        if (!is_string($receivedMessage)) {
            throw new InvalidArgumentException('$receivedMessage必须为字符串或者数组');
        } else {
            $this->rawReceivedMessage = $receivedMessage;
            $this->receivedMessage = (array)simplexml_load_string($receivedMessage, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (!$this->receivedMessage) {
                throw new XMLParseErrorException($receivedMessage);
            }
        }
        $this->token = $token;
        $this->appId = $appId;
        if (!is_array($aesKey)) {
            $this->aesKey = [$aesKey];
        } elseif (is_array($aesKey)) {
            $this->aesKey = $aesKey;
        }
        $this->originId = $originId;
        $this->isSecure = $isSecure;
        $this->msgCrypt = $msgCrypt;
        $this->msgSignature = $msgSignature;
    }

    /**
     * 生成微信请求signature
     *
     * @param string $token
     * @param string $timestamp
     * @param string $nonce
     * @return string
     */
    public static function generateSignature($token, $timestamp, $nonce)
    {
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $signature = sha1($tmpStr);
        return $signature;
    }


    /**
     * 对微信消息进行验证
     *
     * @return bool 成功时返回true
     * @throws \Huying\WechatHelper\Exceptions\SignatureWrongException
     * @throws \Huying\WechatHelper\Exceptions\MsgCryptException
     * @throws \Huying\WechatHelper\Exceptions\XMLParseErrorException
     */
    public function validate()
    {
        $generatedSignature = self::generateSignature($this->token, $this->timestamp, $this->nonce);
        if ($generatedSignature != $this->signature) {
            throw new SignatureWrongException($this->signature, $this->token, $this->timestamp, $this->nonce);
        }
        if ($this->isSecure) {
            $plainMessage = $this->msgCrypt->decryptMsg($this->msgSignature, $this->timestamp, $this->nonce, $this->rawReceivedMessage);
            $this->receivedMessage = (array)simplexml_load_string($plainMessage, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (!$this->receivedMessage) {
                throw new XMLParseErrorException($plainMessage);
            }
        }
    }

    /**
     * 回返解析后的接受到的消息
     *
     * @return array
     */
    public function getReceivedMessage()
    {
        return $this->receivedMessage;
    }

    /**
     * 设置微信回复的文本
     *
     * @param string $content
     * @return $this
     * @throws \InvalidArgumentException $content为空时抛出
     */
    public function text($content)
    {
        if (!$content) {
            throw new InvalidArgumentException('文本内容不能为空');
        }
        $this->replyMessage = [
            'ToUserName' => $this->fromUserName,
            'FromUserName' => $this->toUserName,
            'CreateTime' => time(),
            'MsgType' => self::MSGTYPE_TEXT,
            'Content' => $content
        ];
        return $this;
    }

    /**
     * 设置微信图片消息回复
     *
     * @param string $mediaId
     * @return $this
     * @throws \InvalidArgumentException $mediaId为空时抛出
     */
    public function picture($mediaId)
    {
        // TODO: Implement picture() method.
    }

    /**
     * 设置微信语音消息回复
     *
     * @param string $mediaId
     * @return $this
     * @throws \InvalidArgumentException $mediaId为空时抛出
     */
    public function voice($mediaId)
    {
        // TODO: Implement voice() method.
    }

    /**
     * 设置微信视频消息回复
     *
     * @param string $mediaId
     * @param string $title = null
     * @param string $description = null
     * @return $this
     * @throws \InvalidArgumentException $mediaId为空时抛出
     */
    public function video($mediaId, $title = null, $description = null)
    {
        // TODO: Implement video() method.
    }

    /**
     * 设置微信音乐消息回复
     *
     * @param string $thumbMediaId
     * @param string $musicUrl
     * @param string $hqMusicUrl
     * @param string $title
     * @param string $description
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function music($thumbMediaId, $musicUrl = null, $hqMusicUrl = null, $title = null, $description = null)
    {
        // TODO: Implement music() method.
    }

    /**
     * 设置微信单图文消息回复
     *
     * @param string $title
     * @param string $description = null
     * @param string $picUrl = null
     * @param string $url
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function singeNews($title, $description = null, $picUrl = null, $url)
    {
        // TODO: Implement singeNews() method.
    }

    /**
     * 设置微信图文消息回复
     *
     * @param array $list
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function news($list)
    {
        // TODO: Implement news() method.
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
            $xml .= "<$key>";
            $xml .= (is_array($val) || is_object($val)) ? self::data_to_xml($val) : self::xmlSafeStr($val);
            list($key,) = explode(' ', $key);
            $xml .= "</$key>";
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

    /**
     * 返回回复给微信服务器的消息
     *
     * @param array $message
     * @return string
     * @throws \Huying\WechatHelper\Exceptions\ReplyNotFoundException
     */
    public function reply($message=null)
    {
        if ($this->replyMessage == null and $message == null) {
            throw new ReplyNotFoundException;
        }
        $replyMessage = $message?:$this->replyMessage;
        if ($this->isSecure) {
            $xmlReplyMessage = $this->msgCrypt->encryptMsg($replyMessage, time(), $this->nonce);
        } else {
            $xmlReplyMessage = $replyMessage;
        }
        return $xmlReplyMessage;
    }

    function __get($name)
    {
        $name = ucfirst($name);
        if (isset($this->receivedMessage[$name])) {
            return $this->receivedMessage[$name];
        } else {
            throw new RuntimeException($name.'不存在');
        }
    }
}
