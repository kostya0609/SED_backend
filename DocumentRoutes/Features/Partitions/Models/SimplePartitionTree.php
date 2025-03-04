<?php
namespace SED\DocumentRoutes\Features\Partitions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SED\DocumentRoutes\Features\Routes\Models\Route;

class SimplePartitionTree extends Model
{
	protected $table = 'l_route_partitions';
	public $timestamps = false;
	protected $casts = [
		'is_active' => 'boolean',
	];
	protected $with = ['children'];

	public function children(): HasMany
	{
		return $this->hasMany(self::class, 'parent_id');
	}
}