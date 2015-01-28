<?php namespace Huying\WechatHelper\Services;

interface MediaServiceInterface
{
    /**
     * 公众号可调用本接口来获取多媒体文件，视频文件不支持下载
     *
     * @param string $mediaId
     * @param string $directory
     * @param string $filename = null
     * @return \SplFileInfo
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     * @throws \Huying\WechatHelper\Exceptions\FileExistsException
     */
    public function getMedia($mediaId, $directory, $filename=null);

    /**
     * 调用本接口来上传图片到微信服务器，上传后服务器会返回对应的media_id，公众号此后可根据该media_id来获取文件
     *
     * 图片（image）: 1M，支持JPG格式
     * 媒体文件在后台保存时间为3天，即3天后media_id失效
     * 成功时返回解析过的JSON数据
     * 失败时抛出异常
     *
     * @param string $path
     * @return array
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function uploadImage($path);

    /**
     * 调用本接口来上传语音到微信服务器，上传后服务器会返回对应的media_id，公众号此后可根据该media_id来获取文件
     *
     * 语音（voice）：2M，播放长度不超过60s，支持AMR\MP3\SPEEX格式
     * 媒体文件在后台保存时间为3天，即3天后media_id失效
     * 成功时返回解析过的JSON数据
     * 失败时抛出异常
     *
     * @param string $path
     * @return array
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function uploadVoice($path);

    /**
     * 调用本接口来上传视频到微信服务器，上传后服务器会返回对应的media_id，公众号此后可根据该media_id来获取文件
     *
     * 视频（video）：10MB，支持MP4格式
     * 媒体文件在后台保存时间为3天，即3天后media_id失效
     * 成功时返回解析过的JSON数据
     * 失败时抛出异常
     *
     * @param string $path
     * @return array
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function uploadVideo($path);

    /**
     * 调用本接口来上传缩略图到微信服务器，上传后服务器会返回对应的media_id，公众号此后可根据该media_id来获取文件
     *
     * 缩略图（thumb）：64KB，支持JPG格式
     * 媒体文件在后台保存时间为3天，即3天后media_id失效。
     * 成功时返回解析过的JSON数据
     * 失败时抛出异常
     *
     * @param string $path
     * @return array
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function uploadThumb($path);

}
