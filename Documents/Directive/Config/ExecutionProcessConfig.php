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

	public static function getRequestCancellationActionId(): int
	{
		return \Config::get('DIRECTIVE_EXECUTION_REQUEST_CANCELLATION_ACTION_ID');
	}

	public static function getRequestChangeDeadlineActionId(): int
	{
		return \Config::get('DIRECTIVE_EXECUTION_REQUEST_CHANGE_DEADLINE_ACTION_ID');
	}

	public static function getRequestChangeExecutorActionId(): int
	{
		return \Config::get('DIRECTIVE_EXECUTION_REQUEST_CHANGE_EXECUTOR_ACTION_ID');
	}
}