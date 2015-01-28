<?php namespace Huying\WechatHelper\Services;

interface CustomServiceInterface
{

    /**
     * 可以通过本接口为公众号添加客服账号，每个公众号最多添加10个客服账号
     *
     * @param string $account
     * @param string $nickname
     * @param string $password
     * @return bool
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function addAccount($account, $nickname, $password);

    /**
     * 可以通过本接口为公众号修改客服账号
     *
     * @param string $account
     * @param string $nickname
     * @param string $password
     * @return bool
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function updateAccount($account, $nickname, $password);

    /**
     * 可以通过本接口为公众号修改客服账号
     *
     * @param string $account
     * @param string $nickname
     * @param string $password
     * @return bool
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function deleteAccount($account, $nickname, $password);

    /**
     * 可调用本接口来上传图片作为客服人员的头像，头像图片文件必须是jpg格式，推荐使用640*640大小的图片以达到最佳效果
     *
     * @param string $account
     * @return bool
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function uploadHeadImg($account);

    /**
     * 获取公众号中所设置的客服基本信息，包括客服工号、客服昵称、客服登录账号
     *
     * @return array
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function getAccountList();

    /**
     * 给微信关注用户发送文本消息，需要用户在48小时内与微信账号有交互，不限发送次数
     *
     * @param string|array $toUser 接受者openid，单个接受者时可使用字符串，多个接受者时使用数组
     * @param string $content
     * @param string $account ＝ null
     * @return bool
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function sendText($toUser, $content, $account=null);

    /**
     * 给微信关注用户发送图片消息，需要用户在48小时内与微信账号有交互，不限发送次数
     *
     * @param string|array $toUser 接受者openid，单个接受者时可使用字符串，多个接受者时使用数组
     * @param string $mediaId
     * @param string $account ＝ null
     * @return bool
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function sendImage($toUser, $mediaId, $account=null);

    /**
     * 给微信关注用户发送语音消息，需要用户在48小时内与微信账号有交互，不限发送次数
     *
     * @param string|array $toUser 接受者openid，单个接受者时可使用字符串，多个接受者时使用数组
     * @param string $mediaId
     * @param string $account ＝ null
     * @return bool
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function sendVoice($toUser, $mediaId, $account=null);

    /**
     * 给微信关注用户发送视频消息，需要用户在48小时内与微信账号有交互，不限发送次数
     *
     * @param string|array $toUser 接受者openid，单个接受者时可使用字符串，多个接受者时使用数组
     * @param array $video 数组中包含media_id、thumb_media_id、title、description
     * @param string $account ＝ null
     * @return bool
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function sendVideo($toUser, $video, $account=null);

    /**
     * 给微信关注用户发送音乐消息，需要用户在48小时内与微信账号有交互，不限发送次数
     *
     * @param string|array $toUser 接受者openid，单个接受者时可使用字符串，多个接受者时使用数组
     * @param array $music 数组中包含musicurl、hqmusicurl、thumb_media_id、title、description
     * @param string $account ＝ null
     * @return bool
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function sendMusic($toUser, $music, $account=null);

    /**
     * 给微信关注用户发送图文消息，图文消息条数限制在10条以内，需要用户在48小时内与微信账号有交互，不限发送次数
     *
     * @param string|array $toUser 接受者openid，单个接受者时可使用字符串，多个接受者时使用数组
     * @param array $news
     * @param string $account ＝ null
     * @return bool
     * @throws \Huying\WechatHelper\Exceptions\WechatInterfaceException
     */
    public function sendNews($toUser, $news, $account=null);

}
