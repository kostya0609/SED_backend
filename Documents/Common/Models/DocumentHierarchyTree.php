<?php
namespace SED\Documents\Common\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DocumentHierarchyTree extends Model
{
	protected $table = 'l_sed_document_hierarchy';
	public $timestamps = false;
	protected $casts = [
		'is_start' => 'boolean',
	];
	protected $with = ['children'];
	protected $hidden = ['commonDocument'];
	protected $appends = ['status_title', 'type_id', 'theme_title'];

	public function children(): HasMany
	{
		return $this->hasMany(DocumentHierarchyTree::class, 'parent_document_id', 'document_id');
	}

	public function commonDocument(): HasOne
	{
		return $this->hasOne(Document::class, 'id', 'document_id');
	}

	public function getStatusTitleAttribute(): string
	{
		return $this->commonDocument->status_title;
	}

	public function getThemeTitleAttribute(): string {
		return $this->commonDocument->theme;
	}

	public function getTypeIdAttribute(): int {
		return $this->commonDocument->type_id;
	}
}