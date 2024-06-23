<?php

namespace SED\Documents\Review\Config;

class DecideProcessConfig
{
	public static function getProcessTemplateId(): int
	{
		return \Config::get('REVIEW_PROCESS_TEMPLATE_ID');
	}

	public static function getDecideGroupId(): int
	{
		return \Config::get('REVIEW_PROCESS_GROUP_ID');
	}
}
