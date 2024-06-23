<?php
namespace SED\Documents\Directive\Models;

use SED\Documents\Common\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class History extends Model
{
	protected $table = 'l_directive_history';
	protected $with = ['user'];
	protected $fillable = [
		'event',
		'user_id',
		'directive_id',
	];

	public function user(): HasOne
	{
		return $this->hasOne(User::class, 'ID', 'user_id');
	}
}