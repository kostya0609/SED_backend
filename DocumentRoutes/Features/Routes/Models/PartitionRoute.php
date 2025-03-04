<?php

namespace SED\DocumentRoutes\Features\Routes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use SED\Common\Models\User;


class PartitionRoute extends Model
{
	protected $table = 'l_partition_route';

	protected $with = [
		'creator',
		'lastEditor',
	];

	public function creator(): HasOne
	{
		return $this->hasOne(User::class, 'ID', 'creator_id');
	}

	public function lastEditor(): HasOne
	{
		return $this->hasOne(User::class, 'ID', 'last_editor_id');
	}
	
}
