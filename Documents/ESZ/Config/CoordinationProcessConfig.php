<?php
namespace SED\Documents\ESZ\Config;

class CoordinationProcessConfig
{
	public static function getTemplateId(): int
	{
		return \Config::get('ESZ_COORDINATION_PROCESS_TEMPLATE_ID');
	}
}