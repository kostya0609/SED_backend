<?php
namespace SED\DocumentRoutes\Features\Routes\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table='l_route_groups';

    public $timestamps = false;
    
    protected $fillable = [
		'title',
	];
}