<?php
namespace SED\Documents\ESZ\Config;

class SigningProcessConfig
{
	public static function getTemplateId(): int
	{
		return \Config::get('ESZ_SIGNING_PROCESS_TEMPLATE_ID');
	}

	public static function getSigningGroupId(): int
	{
		return \Config::get('ESZ_SIGNING_PROCESS_GROUP_ID');
	}
}