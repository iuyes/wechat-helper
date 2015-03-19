<?php namespace Huying\WechatHelper\Services;
class test1
{
	public function __call($method, $arg)
	{
		echo '你想调用我不存在的方法',$method,'方法<br/>';  
        echo '还传了一个参数<br/>';  
        echo print_r($arg),'<br/>';  
	}

	public function add($a, $b)
	{
		return $a+$b;
	}

}