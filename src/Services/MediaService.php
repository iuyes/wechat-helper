<?php namespace Huying\WechatHelper\Services;

class MediaService extends BaseService
{
    public function __construct($appid, $appsecret, $client, $history)
    {
        parent::__construct($appid, $appsecret, $client, $history);
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
        $img = self::wechatInterfaceGet($url, true);
        if (substr($directory, -1) != '/') {
            $directory = $directory.'/';
        }
        if ($filename === null) {
            $filename = md5($img->getBody()).'.jpg';
        }
        $file = fopen($directory.$filename, 'w');
        fwrite($file, $img->getBody());
        fclose($file);
        return true;
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
        //$res = self::wechatInterfacePost($url, array('media' => '@'.$file_path));
        //return $res;
        //$data = array('media' => '@'.$file_path);
        /*$data = 'media=@'.$file_path;

        var_dump($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        # curl_setopt( $ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($ch);
        if (!$data)
        {
            error_log(curl_error($ch));
        }
        curl_close($ch);
        return $data;*/


        $command = 'curl -F media=@'.$file_path.' "'.$url.'"';
        var_dump($command);
        return exec($command, $out);


    }

}
