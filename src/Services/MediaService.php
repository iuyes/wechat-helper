<?php namespace Huying\WechatHelper\Services;

class MediaService
{
    protected $access_token;
    public function __construct($access_token = null)
    {
        if ($access_token) {
            $this->access_token = $access_token;   
        } else {
            $this->access_token = BasicService::getAccessToken();
        }
        
    }
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
    public function getMedia($mediaId, $directory, $filename=null)
    {
        $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->access_token.'&media_id='.$mediaId;
    }

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
    public function uploadMedia($type, $file_path)
    {
        $url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->access_token.'&type='.$type;
        $res = BasicService::wechatInterfacePost($url, array('media' => '@'.$file_path));
        //$key = 
        //$expire_mins = 3*24*60 - 60;
        //Cache::put($key, $res['access_token'], $expire_mins);
        return $res;
    }

    public function getMediaId($filename)
    {
        
    }

}
