<?php
namespace SED\Common\Config;

class SEDConfig
{
	public static function getModuleName(): string
	{
		return 'SED';
	}

	public static function getNotificationSender()
	{
		return 15490;
	}
}