<?php
namespace SED\Documents\ESZ\Config;

class ResolutionProcessConfig
{
	public static function getTemplateId(): int
	{
		return \Config::get('ESZ_RESOLUTION_PROCESS_TEMPLATE_ID');
	}

	public static function getResolutionGroupId(): int
	{
		return \Config::get('ESZ_RESOLUTION_PROCESS_GROUP_ID');
	}
}