<?php
namespace SED\Documents\Common\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DocumentHierarchy extends Model
{
	protected $table = 'l_sed_document_hierarchy';
	public $timestamps = false;
	protected $fillable = [
		'document_id',
		'parent_document_id',
		'is_start',
		'concrete_document_id',
		'number',
	];
	protected $casts = [
		'is_start' => 'boolean',
	];
	protected $appends = ['status_title'];

	public function getStatusTitleAttribute(): string
	{
		return $this->commonDocument->status_title;
	}

	public function commonDocument(): HasOne
	{
		return $this->hasOne(Document::class, 'id', 'document_id');
	}
}