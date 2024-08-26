<?php

namespace SED\DocumentRoutes\Features\Routes\Models;

use Illuminate\Database\Eloquent\Model;

class RouteAvailability extends Model
{
    protected $table = 'l_route_availability';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'route_id',
        'department_id'
    ];
}
