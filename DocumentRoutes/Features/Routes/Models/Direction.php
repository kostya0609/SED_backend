<?php
namespace SED\DocumentRoutes\Features\Routes\Models;

use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    protected $table='l_route_directions';

    public $timestamps = false;

    protected $fillable = [
		'title',
	];
}