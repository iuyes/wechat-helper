<?php namespace Huying\WechatHelper\Services;

interface CallbackServiceInterface
{
    /**
     * 生成微信请求signature
     *
     * @param string $token
     * @param string $timestamp
     * @param string $nonce
     * @return string
     */
    public static function generateSignature($token, $timestamp, $nonce);

    /**
     * 对微信消息进行验证
     *
     * @return bool 成功时返回true
     * @throws \Huying\WechatHelper\Exceptions\SignatureWrongException
     * @throws \Huying\WechatHelper\Exceptions\MsgCryptException
     */
    public function validate();

    /**
     * 回返解析后的接受到的消息
     *
     * @return array
     */
    public function getReceivedMessage();

    /**
     * 设置微信回复的文本
     *
     * @param string $content
     * @return $this
     * @throws \InvalidArgumentException $content为空时抛出
     */
    public function text($content);

    /**
     * 设置微信图片消息回复
     *
     * @param string $mediaId
     * @return $this
     * @throws \InvalidArgumentException $mediaId为空时抛出
     */
    public function picture($mediaId);

    /**
     * 设置微信语音消息回复
     *
     * @param string $mediaId
     * @return $this
     * @throws \InvalidArgumentException $mediaId为空时抛出
     */
    public function voice($mediaId);

    /**
     * 设置微信视频消息回复
     *
     * @param string $mediaId
     * @param string $title = null
     * @param string $description = null
     * @return $this
     * @throws \InvalidArgumentException $mediaId为空时抛出
     */
    public function video($mediaId, $title=null, $description=null);

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
    public function music($thumbMediaId, $musicUrl=null, $hqMusicUrl=null, $title=null, $description=null);

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
    public function singeNews($title, $description=null, $picUrl=null, $url);

    /**
     * 设置微信图文消息回复
     *
     * @param array $list
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function news($list);

    /**
     * 返回回复给微信服务器的消息
     *
     * @param array $message = null
     * @return string
     * @throws \Huying\WechatHelper\Exceptions\ReplyNotFoundException
     */
    public function reply($message=null);
}
