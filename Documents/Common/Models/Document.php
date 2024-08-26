<?php
namespace SED\Documents\Common\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


/**
 * @property int $id
 * @property int $document_id
 * @property string $number
 * @property int $type_id
 * @property string $theme
 * @property int $initiator_id
 * @property string $status_title
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * 
 * @property DocumentType $type
 * @property User $initiator
 * @property Participant $participants
 */
class Document extends Model
{
	protected $table = 'l_sed_documents';
	protected $fillable = [
		'document_id',
		'number',
		'type_id',
		'theme',
		'initiator_id',
		'status_title',
	];

	public function type(): BelongsTo
	{
		return $this->belongsTo(DocumentType::class);
	}

	public function initiator(): HasOne
	{
		return $this->hasOne(User::class, 'ID', 'initiator_id');
	}

	public function participants(): HasMany
	{
		return $this->hasMany(Participant::class);
	}
}