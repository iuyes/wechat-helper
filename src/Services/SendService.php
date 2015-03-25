<?php namespace Huying\WechatHelper\Services;

class SendService
{
    protected $base_service;

    public function __construct(BaseService $base_service)
    {
        $this->base_service = $base_service;
    }


    public function uploadNews($articles)
    {
    	$data = json_encode(['articles' => $articles]);
    	$url = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token='.$this->base_service->access_token;
    	$res = $this->base_service->wechatInterfacePost($url, $data);
    	return $res;
    }

    public function sendByGroup($message, $group_id = null)
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$this->base_service->access_token;
    	if (isset($group_id)) {
    		$message['filter'] = ['is_to_all' => false, 'group_id' => $group_id];
    	} else {
    		$message['filter'] = ['is_to_all' => true];
    	}
    	$data = json_encode($message);
    	$res = $this->base_service->wechatInterfacePost($url, $data);
    	return $res['msg_id'];
    }

    public function sendById($message, $id)
    {
    	$message['touser'] = $id;
    	if (is_array($id)) {
    		$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$this->base_service->access_token;
    	} else {
    		$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token='.$this->base_service->access_token;
    	}	
    	$data = json_encode($message);
    	$res = $this->base_service->wechatInterfacePost($url, $data);
        if (isset($res['msg_id'])) {
            return $res['msg_id'];
        } else {
            return $res;
        }
    	
    }


    public function getGroupVideoId($media_id, $title = null, $description = null)
    {
        $url = 'https://file.api.weixin.qq.com/cgi-bin/media/uploadvideo?access_token='.$this->base_service->access_token;
    	$data['media_id'] = $media_id;
    	if ($title) {
    		$data['title'] = $title;
    	}
    	if ($description) {
    		$data['description'] = $description;
    	}
    	$data = json_encode($data1);
    	$res = $this->base_service->wechatInterfacePost($url, $data);
    	return $res['media_id'];
    }

    public function sendNewsByGroup($media_id, $group_id = null)
    {
    	$message['mpnews'] = ['media_id' => $media_id];
    	$message['msgtype'] = 'mpnews';
    	return self::sendByGroup($message, $group_id);
    }

    public function sendTextByGroup($content, $group_id = null)
    {
    	$message['text'] = ['content' => $content];
    	$message['msgtype'] = 'text';
    	return self::sendByGroup($message, $group_id);
    }

    public function sendImageByGroup($media_id, $group_id = null)
    {
    	$message['image'] = ['media_id' => $media_id];
    	$message['msgtype'] = 'image';
    	return self::sendByGroup($message, $group_id);
    }

    public function sendVideoByGroup($media_id, $group_id = null, $title = null, $description = null)
    {
    	$group_media_id = self::getGroupVideoId($media_id, $title, $description);
    	$message['mpvideo'] = ['media_id' => $group_media_id];
    	$message['msgtype'] = 'mpvideo';
    	return self::sendByGroup($message, $group_id);
    }

    public function sendNewsById($media_id, $id)
    {
    	$message['mpnews'] = ['media_id' => $media_id];
    	$message['msgtype'] = 'mpnews';
    	return self::sendById($message, $id);
    }

    public function sendTextById($content, $id)
    {
    	$message['text'] = ['content' => $content];
    	$message['msgtype'] = 'text';
    	return self::sendById($message, $id);
    }

    public function sendVoiceById($media_id, $id)
    {
    	$message['voice'] = ['media_id' => $media_id];
    	$message['msgtype'] = 'voice';
    	return self::sendById($message, $id);
    }

    public function sendImageById($media_id, $id)
    {
    	$message['image'] = ['media_id' => $media_id];
    	$message['msgtype'] = 'image';
    	return self::sendById($message, $id);
    }

    public function sendVideoById($media_id, $id, $title = null, $description = null)
    {
    	$group_media_id = self::getGroupVideoId($media_id, $title, $description);
    	if (is_array($id)) {
    		$message['video'] = ['media_id' => $group_media_id];
    	    if ($title) {
    	    	$message['video']['title'] = $title;
    	    }
    	    if ($description) {
    	    	$message['video']['description'] = $description;
    	    }
    	    $message['msgtype'] = 'video';
    	} else {
    		$message['mpvideo'] = ['media_id' => $group_media_id];
    		$message['msgtype'] = 'video';
    	}
    	
    	return self::sendById($message, $id);
    }

    public function delGroupMessage($msg_id)
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/delete?access_token='.$this->base_service->access_token;
    	$data = json_encode(['msg_id' => $msg_id]);
    	$this->base_service->wechatInterfacePost($url, $data);
    	return true;
    }

    public function getGroupMessageStatus($msg_id)
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/get?access_token='.$this->base_service->access_token;
    	$data = json_encode(['msg_id' => $msg_id]);
    	$res = $this->base_service->wechatInterfacePost($url, $data);
    	return $res['msg_status'];
    }


}