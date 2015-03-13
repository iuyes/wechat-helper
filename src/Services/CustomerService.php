<?php namespace Huying\WechatHelper\Services\CustomerService;

use Huying\WechatHelper\Services\BasicService\BasicService;

class CustomerService
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

	public function getList()
	{
		$url = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token='.$this->access_token;
		$res = BasicService::wechatInterfaceGet($url);
		return $res['kf_list'];
	}

	public function getOnlineList()
	{
		$url = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token='.$this->access_token;
		$res = BasicService::wechatInterfaceGet($url);
		return $res['kf_online_list'];
	}

	public function addAccount($kf_account, $nickname, $pwd)
	{
		$url = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token='.$this->access_token;
		$data = array(
			'kf_account'	=>	$kf_account;
			'nickname'		=>	$nickname;
			'password'		=>	$pwd;
			);
		BasicService::wechatInterfacePost($url, $data);
		return true;
	}

	public function uploadHeadImg($kf_account, $file_path)
	{
		$url = 'http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token='.$this->access_token.
		'&kf_account='.$kf_account;
		BasicService::wechatInterfacePost($url, array('media' => '@'.$file_path));
		return true;
	}

	public function delAccount($kf_account)
	{
		$url = 'https://api.weixin.qq.com/customservice/kfaccount/del?access_token='.$this->access_token.
		'&kf_account='.$kf_account;
		BasicService::wechatInterfaceGet($url);
		return true;
	}

	public function kfSend($openid, $type, $content, $kf_account = null)
	{
		$data['touser'] = $openid;
		$data['msgtype'] = $type;
		$data[$type] = $content;
		if (isset($kf_account)) {
			$data['customservice'] = ['kf_account' => $kf_account];
		}
		$json = json_encode($data);
		BasicService::wechatInterfacePost($url, $json);
		return true;
	}

	public function createkfSession($openid, $kf_account, $text = null)
	{
		$url = 'https://api.weixin.qq.com/customservice/kfsession/create?access_token='.$this->access_token;
		$data = array(
			'openid'		=>	$openid,
			'kf_account'	=>	$kf_account
			);
		if(isset($text)) {
			$data['text'] = $text;
		}
		$json = json_encode($data);
		BasicService::wechatInterfacePost($url, $json);
		return true;
	}

	public function closekfSession($openid, $kf_account, $text = null)
	{
		$url = 'https://api.weixin.qq.com/customservice/kfsession/close?access_token='.$this->access_token;
		$data = array(
			'openid'		=>	$openid,
			'kf_account'	=>	$kf_account
			);
		if(isset($text)) {
			$data['text'] = $text;
		}
		$json = json_encode($data);
		BasicService::wechatInterfacePost($url, $json);
		return true;
	}

	public function getkfSessionState($openid)
	{
		$url = 'https://api.weixin.qq.com/customservice/kfsession/getsession?access_token='.$this->access_token.
		'&openid='.$openid;
		$res = BasicService::wechatInterfaceGet($url);
		return $res;
	}

	public function getkfSessionState($kf_account)
	{
		$url = 'https://api.weixin.qq.com/customservice/kfsession/getsessionlist?access_token='.$this->access_token.
		'&kf_account='.$kf_account;
		$res = BasicService::wechatInterfaceGet($url);
		return $res['sessionlist'];
	}

	public function getkfWaitList()
	{
		$url = 'https://api.weixin.qq.com/customservice/kfsession/getwaitcase?access_token='.$this->access_token;
		$res = BasicService::wechatInterfaceGet($url);
		return $res;
	}

	public function getRecord($start_time, $end_time, $page_index = 1, $openid = null, $page_size = 10)
	{
		$url = 'https://api.weixin.qq.com/cgi-bin/customservice/getrecord?access_token='.$this->access_token;
		$data = array(
			'starttime'	=>	$start_time,
			'endtime'	=>	$end_time,
			'pagesize'	=>	$page_size,
			'pageindex'	=>	$page_index
			);
		if (isset($openid)) {
			$data['openid'] = $openid;
		}
		$json = json_encode($data);
		$res = BasicService::wechatInterfacePost($url, $json);
		return $res['recordlist'];
	}

}