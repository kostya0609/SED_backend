<?php
namespace SED\Documents\Directive\Config;

class ExecutionControlProcessConfig
{
	public static function getTemplateId(): int
	{
		return \Config::get('DIRECTIVE_EXECUTION_CONTROL_PROCESS_TEMPLATE_ID');
	}

	public static function getControlGroupId(): int
	{
		return \Config::get('DIRECTIVE_EXECUTION_CONTROL_PROCESS_GROUP_ID');
	}
}