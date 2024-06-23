<?php
namespace SED\Documents\Directive\Dto;

class CreateHistoryDto
{
	public int $directive_id;
	public int $user_id;
	public string $event;
}