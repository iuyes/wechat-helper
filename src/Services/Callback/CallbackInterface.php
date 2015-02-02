<?php
namespace Huying\WechatHelper\Services;

interface CallbackInterface
{
	public function reply($message, $touser, $fromuser);
}
