<?php
namespace SED\DocumentRoutes\Features\Partitions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SED\DocumentRoutes\Features\Routes\Models\Route;

class Partition extends Model
{
	protected $table = 'l_route_partitions';
	
	protected $casts = [
		'is_active' => 'boolean',
	];
	
	protected $fillable = [
		'parent_id',
		'title',
		'creator',
		'lastEditor',
		'is_active'
	];

	public function children(): HasMany
	{
		return $this->hasMany(self::class, 'parent_id')->with('routes');
	}

	public function routes(): HasMany
	{
		return $this->hasMany(Route::class);
	}
}