<?php
namespace SED\Documents\Directive\Enums;

class Status
{
	public const PREPARATION = 1;
	public const EXECUTION_CHANGE_REQUEST = 2;
	public const EXECUTION_IN_WORK = 3;
	public const EXECUTION_CONTROL = 4;
	public const ARCHIVE_WORKED = 5;
	public const ARCHIVE_CANCELLED = 6;
}