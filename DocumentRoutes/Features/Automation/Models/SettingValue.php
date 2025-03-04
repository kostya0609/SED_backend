<?php
namespace SED\DocumentRoutes\Features\Automation\Models;

use Illuminate\Database\Eloquent\Model;

class SettingValue extends Model
{
	protected $table = 'l_route_setting_values';
	protected $primaryKey = false;
	public $incrementing = false;
	protected $fillable = ['setting_id', 'tmp_doc_id', 'is_active', 'data'];
	public $timestamps = false;
	protected $casts = [
		'is_active' => 'boolean',
		'data' => 'object',
	];
}