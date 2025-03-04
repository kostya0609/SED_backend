<?php
namespace SED\Documents\Review\Models;

use \Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use SED\DocumentRoutes\DocumentTemplate;
use \App\Modules\Departments\Models\Department;
use SED\Documents\Review\Enums\{ParticipantType, FileType, Status};
use Illuminate\Database\Eloquent\Relations\{HasMany, HasOne, BelongsTo};
use SED\Documents\Common\Models\{DocumentType, DocumentTheme, Document, DocumentHierarchy, DocumentHierarchyTree};

/**
 * @property int $id
 * @property string $number
 * @property int $type_id
 * @property int $status_id
 * @property int $parent_id
 * @property int $content_id
 * @property int $process_template_id
 * @property int $department_id
 * @property \App\Modules\Departments\Models\Department $department
 * @property Participant $initiator
 * @property \Illuminate\Support\Collection $receivers
 * @property StatusModel $status
 * @property DocumentTheme $theme
 * @property Contents $contents
 * @property ReviewFile $mainFiles
 * @property History $history
 * @property ProcessHistory $processHistory
 * @property DocumentType $type
 * @property string $theme_title
 * @property ?int $common_document_id
 * @property ?int $tmp_doc_id
 * @property DocumentTemplate $templateDocument
 * @property ?string $theme
 * @property Document $commonDocument
 * @property DocumentHierarchy $documentHierarchy
 * @property DocumentHierarchy $parent_document
 * @property Collection $hierarchy
 */
class Review extends Model
{
	protected $table = 'l_review';
	protected $with = [
		'type',
		'status',
		'contents',
		'department',
		'mainFiles',
		'initiator',
		'receivers',
		'history',
		'processHistory',
	];
	protected $appends = ['theme', 'parent_document', 'hierarchy'];
	protected $hidden = ['documentHierarchy', 'theme_title'];

	public function type(): HasOne
	{
		return $this->hasOne(DocumentType::class, 'id', 'type_id');
	}

	public function status(): HasOne
	{
		return $this->hasOne(StatusModel::class, 'id', 'status_id');
	}

	public function contents(): HasOne
	{
		return $this
			->hasOne(Contents::class)
			->withDefault(['content' => null, 'portfolio' => null]);
	}

	public function mainFiles(): HasMany
	{
		return $this
			->hasMany(ReviewFile::class)
			->where('type_id', FileType::MAIN);
	}

	public function department(): HasOne
	{
		return $this->hasOne(Department::class, 'ID', 'department_id');
	}

	public function initiator(): HasOne
	{
		return $this
			->hasOne(Participant::class, 'review_id', 'id')
			->where('type_id', ParticipantType::INITIATOR)
			->withDefault(['type_id' => ParticipantType::INITIATOR]);
	}

	public function receivers(): HasMany
	{
		return $this
			->hasMany(Participant::class, 'review_id', 'id')
			->where('type_id', ParticipantType::RECEIVERS);
	}

	public function history(): HasMany
	{
		return $this->hasMany(History::class, 'review_id', 'id');
	}

	public function processHistory(): HasMany
	{
		return $this->hasMany(ProcessHistory::class, 'review_id', 'id');
	}

	public function templateDocument(): BelongsTo
	{
		return $this->belongsTo(DocumentTemplate::class, 'tmp_doc_id');
	}

	public function getThemeAttribute(): ?string
	{
		return isset($this->templateDocument) ? $this->templateDocument->title : $this->theme_title;
	}

	public function commonDocument(): BelongsTo
	{
		return $this->belongsTo(Document::class, 'common_document_id');
	}
	public function documentHierarchy(): HasOne
	{
		return $this->hasOne(DocumentHierarchy::class, 'document_id', 'common_document_id');
	}

	public function getParentDocumentAttribute(): ?Document
	{
		if ($this->documentHierarchy && $this->documentHierarchy->parentDocument) {
			return $this->documentHierarchy->parentDocument->commonDocument;
		}

		return null;
	}

	public function getHierarchyAttribute(): Collection
	{
		$start_document_id = $this->documentHierarchy ? $this->documentHierarchy->start_document_id : null;

		if (!$start_document_id) {
			return collect([]);
		}

		$hierarchy = DocumentHierarchyTree::firstWhere('start_document_id', $start_document_id);

		if (!$hierarchy) {
			return collect([]);
		}

		return collect([$hierarchy]);
	}

	public function isPreparation(): bool
	{
		return $this->status_id === Status::PREPARATION;
	}

	public function isReview(): bool
	{
		return $this->status_id === Status::REVIEW;
	}

	public function isArchiveWorked(): bool
	{
		return $this->status_id === Status::ARCHIVE_WORKED;
	}

	public function isArchiveCancelled(): bool
	{
		return $this->status_id === Status::ARCHIVE_CANCELLED;
	}

	public function isDraft(): bool
	{
		return $this->status_id === Status::DRAFT;
	}
}
