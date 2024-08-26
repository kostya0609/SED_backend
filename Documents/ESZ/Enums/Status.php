<?php
namespace SED\Documents\ESZ\Enums;

class Status
{
	/**
	 * Подготовка, на подготовке
	 */
	public const PREPARATION = 1;

	/**
	 * Устранение замечаний
	 */
	public const FIX = 2;
	
	/**
	 * Согласование
	 */
	public const COORDINATION = 3;
		
	/**
	 * Устранение замечаний после подписания
	 */
	public const FIX_SIGNING = 4;
	
	/**
	 * Подписание
	 */
	public const SIGNING = 5;
	
	/**
	 * Устранение замечаний на резолюции
	 */
	public const FIX_RESOLUTION = 6;
	
	/**
	 * Наложение резолюции
	 */
	public const RESOLUTION = 7;

	/**
	 * Архив отработанно
	 */
	public const ARCHIVE_WORKED = 8;
	
	/**
	 * Архив аннулированно
	 */
	public const ARCHIVE_CANCELLED = 9;
}