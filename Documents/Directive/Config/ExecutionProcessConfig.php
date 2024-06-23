<?php
namespace SED\Documents\Directive\Config;

class ExecutionProcessConfig
{
	public static function getTemplateId(): int
	{
		return \Config::get('DIRECTIVE_EXECUTION_PROCESS_TEMPLATE_ID');
	}

	public static function getDecideGroupId(): int
	{
		return \Config::get('DIRECTIVE_EXECUTION_PROCESS_GROUP_ID');
	}
}