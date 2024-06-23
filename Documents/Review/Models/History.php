<?php
namespace SED\Documents\Review\Models;

use SED\Documents\Common\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class History extends Model
{
	protected $table = 'l_review_history';
	protected $with = ['user'];
	protected $fillable = [
		'event',
		'user_id',
		'review_id',
	];

	public function user(): HasOne
	{
		return $this->hasOne(User::class, 'ID', 'user_id');
	}
}