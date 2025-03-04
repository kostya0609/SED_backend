<?php
namespace SED\Common\Exceptions;

class AccessDeniedException extends \DomainException
{
	public function __construct(string $message = 'Доступ запрещен!')
	{
		parent::__construct($message);
	}
}