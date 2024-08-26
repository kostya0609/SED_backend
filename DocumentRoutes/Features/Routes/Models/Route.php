<?php

namespace SED\DocumentRoutes\Features\Routes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use SED\Common\Models\User;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;
use SED\DocumentRoutes\Features\Partitions\Models\Partition;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $creator_id
 * @property int $last_editor_id
 * @property int $direction_id
 * @property int $group_id
 * @property int $partition_id
 * @property boolean $is_active
 * @property Direction $direction
 * @property Group $group
 * @property RouteAvailability[] $departments
 * @property DocumentTemplate[] $documentTemplates
 * @property Partition $partition
 */
class Route extends Model
{
	protected $table = 'l_route_routes';

	protected $casts = [
        'is_active' => 'boolean',
    ];

	protected $with = [
		'group',
		'direction',
		'partition',
		'departments',
		'creator',
		'lastEditor',
		'documentTemplates',
	];

	public function direction(): BelongsTo
	{
		return $this->belongsTo(Direction::class);
	}

	public function group(): BelongsTo
	{
		return $this->belongsTo(Group::class);
	}

	public function partition(): BelongsTo
	{
		return $this->belongsTo(Partition::class);
	}

	public function departments(): HasMany
	{
		return $this->hasMany(RouteAvailability::class);
	}

	public function documentTemplates(): HasMany
	{
		return $this->hasMany(DocumentTemplate::class);
	}

	public function creator(): HasOne
	{
		return $this->hasOne(User::class, 'ID', 'creator_id');
	}

	public function lastEditor(): HasOne
	{
		return $this->hasOne(User::class, 'ID', 'last_editor_id');
	}
}
