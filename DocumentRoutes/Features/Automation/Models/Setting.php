<?php
namespace SED\DocumentRoutes\Features\Automation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Setting extends Model
{
	protected $table = 'l_route_settings';
	protected $fillable = ['title', 'description', 'default_is_active', 'default_data'];
	public $timestamps = false;
	protected $casts = [
		'default_is_active' => 'boolean',
		'default_data' => 'object',
	];
}