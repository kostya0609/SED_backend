<?php
namespace SED\Documents\Directive\Models;

use SED\Common\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne};


/**
 * @property int $directive_id
 * @property int $type_id
 * @property int $user_id
 * @property User $user
 * @property ParticipantTypeModel $type
 */
class Participant extends Model
{
	protected $table = 'l_directive_participants';
	public $timestamps = false;
	protected $fillable = [
		'type_id',
		'user_id',
	];
	protected $with = ['user'];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id', 'ID');
	}

	public function type(): HasOne
	{
		return $this->hasOne(ParticipantTypeModel::class, 'id', 'type_id');
	}
}