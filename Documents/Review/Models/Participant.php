<?php
namespace SED\Documents\Review\Models;

use SED\Common\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne};


/**
 * @property int $id
 * @property int $review_id
 * @property int $type_id
 * @property int $user_id
 * @property bool $can_deletable
 * @property User $user
 * @property ParticipantTypeModel $type
 */
class Participant extends Model
{
	protected $table = 'l_review_participants';
	public $timestamps = false;
	public $fillable = [
		'type_id',
		'user_id',
		'can_deletable',
	];

    protected $with = ['user'];

	protected $casts = [
		'can_deletable' => 'boolean',
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id', 'ID');
	}

	public function type(): HasOne
	{
		return $this->hasOne(ParticipantTypeModel::class, 'id', 'type_id');
	}
}
