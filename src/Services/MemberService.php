<?php namespace Huying\WechatHelper\Services;


class MemberService extends BaseService
{
    public function __construct($appid, $appsecret, $client, $history)
    {
        parent::__construct($appid, $appsecret, $client, $history);
    }

    public function getUserList($next_openid = null)
    {
        if (isset($next_openid)) {
            $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$this->access_token}&next_openid={$next_openid}";
        } else {
            $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$this->access_token}";
        }
        $res = self::wechatInterfaceGet($url);
        return $res;
    }
    
    public function createGroup($name)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token='.$this->access_token;
        $json = json_encode(['group' =>  ['name' => urlencode($name)]]);
        $res = self::wechatInterfacePost($url, $json);
        return $res['group'];
    }

    public function getGroups()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token='.$this->access_token;
        $res = self::wechatInterfaceGet($url);
        return $res['groups'];
    }

    public function getGroupFromId($openid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/getid?access_token='.$this->access_token;
        $json = json_encode(['openid' => $openid]);
        $res = self::wechatInterfacePost($url, $json);
        return $res['groupid'];
    }

    public function modifyGroupName($group_id, $name)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/update?access_token='.$this->access_token;
        $json = json_encode(['group' => ['id' => $group_id, 'name' => urlencode($name)]]);
        self::wechatInterfacePost($url, $json);
        return true;
    }

    public function modifyGroupForMember($openid, $groupid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token='.$this->access_token;
        $json = json_encode(['openid' => $openid, 'to_groupid' => $groupid]);
        self::wechatInterfacePost($url, $json);
        return true;
    }

    public function modifyGroupForMembers($openid_list, $groupid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate?access_token='.$this->access_token;
        $json = json_encode(['openid_list' => $openid_list, 'to_groupid' => $groupid]);
        self::wechatInterfacePost($url, $json);
        return true;
    }

    public function modifyMemberRemark($remark, $openid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token='.$this->access_token;
        $json = json_encode(['openid' => $openid, 'remark' => urlencode($remark)]);
        self::wechatInterfacePost($url, $json);
        return true;
    }

    public function getMemberInfo($openid, $lang = 'zh_CN')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$openid.'&lang='.$lang;
        return self::wechatInterfaceGet($url);
    }

    public function getOauthCode($redirect_url, $state = 0, $scope = 0, $appid = null)
    {
        $scope = $scope ? 'snsapi_userinfo' : 'snsapi_base';
        $redirect_url = urlencode($redirect_url);
        $appid = $appid ?: Config::get('test.appId');
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_url}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
        header('Location: '.$url);
        return true;
    }

    public function getOauthToken($code, $appid = null, $appsecret = null)
    {
        $appid = $appid ?: Config::get('test.appId');
        $key = 'oauth_token_'.$appid;
        if (Cache::has($key)) {
            return Cache::get($key);
        } else {
            $refresh_key = 'oauth_refresh_token_'.$appid;
            if (Cache::has($refresh_key)) {
                $refresh_token = Cache::get($refresh_key);
                $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$appid}&grant_type=refresh_token&refresh_token={$refresh_key}";
                $res = self::wechatInterfaceGet($url);
                $expire_mins = $res['expires_in']/60-1;
                Cache::put($key, $res['access_token'], $expire_mins);
                return $res['access_token'];
            } else {
                $appsecret = $appsecret ?: Config::get('test.appsecret');
                $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
                $res = self::wechatInterfaceGet($url);
                $expire_mins = $res['expires_in']/60-1;
                Cache::put($key, $res['access_token'], $expire_mins);
                $refresh_expire_mins = 7*24*60 - 1;
                Cache::put($key, $res['refresh_token'], $refresh_expire_mins);
                return $res['access_token'];
            }
        }       
    }

    public function getOauthMemberInfo($oauth_token, $openid, $lang = 'zh_CN')
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$oauth_token}&openid={$openid}&lang={$lang}";
        return self::wechatInterfaceGet($url);
    }

    public function checkOauthToken($oauth_token, $openid)
    {
        $url = "https://api.weixin.qq.com/sns/auth?access_token={$oauth_token}&openid={$openid}";
        self::wechatInterfaceGet($url);
        return true;
    }



}
