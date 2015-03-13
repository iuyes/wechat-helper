<?php namespace Huying\WechatHelper\Services;


class MemberService
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
    
    public function createGroup($name)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token='.$this->access_token;
        $json = json_encode(['group' =>  ['name' => $name]]);
        $res = BasicService::wechatInterfacePost($url, $json);
        return $res['group'];
    }

    public function getGroups()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token='.$this->access_token;
        $res = BasicService::wechatInterfaceGet($url);
        return $res['groups'];
    }

    public function getGroupFromId($openid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/getid?access_token='.$this->access_token;
        $json = json_encode(['openid' => $openid]);
        $res = BasicService::wechatInterfacePost($url, $json);
        return $res['groupid'];
    }

    public function modifyGroupName($group_id, $name)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/update?access_token='.$this->access_token;
        $json = json_encode(['group' => ['id' => $group_id, 'name' => $name]]);
        BasicService::wechatInterfacePost($url, $json);
        return true;
    }

    public function modifyGroupForMember($openid, $groupid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token='.$this->access_token;
        $json = json_encode(['openid' => $openid, 'to_groupid' => $groupid]);
        BasicService::wechatInterfacePost($url, $json);
        return true;
    }

    public function modifyGroupForMembers($openid_list, $groupid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate?access_token='.$this->access_token;
        $json = json_encode(['openid_list' => $openid_list, 'to_groupid' => $groupid]);
        BasicService::wechatInterfacePost($url, $json);
        return true;
    }



}
