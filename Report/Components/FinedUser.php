<?php
namespace SED\Report\Components;

use Illuminate\Database\Eloquent\Model;

class FinedUser
{
	public int $user_id;
	public string $full_name;
	public string $link;

	public function __construct(Model $user)
	{
		$this->user_id = $user->id;
		$this->full_name = $user->full_name;
		$this->link = $user->link;
	}
}