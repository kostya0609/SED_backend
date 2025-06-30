<?php
namespace SED\Documents\ESZ\Models;

use \Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use SED\DocumentRoutes\DocumentTemplate;
use \App\Modules\Departments\Models\Department;
use App\Modules\DocumentsHierarchy\DocumentsHierarchyFacade;
use SED\Documents\ESZ\Enums\{ParticipantType, FileType, Status};
use Illuminate\Database\Eloquent\Relations\{HasMany, HasOne, BelongsTo};
use SED\Documents\Common\Models\{DocumentType, Document, DocumentHierarchy, DocumentHierarchyTree};

/**
 * @property int $id
 * @property string $number
 * @property int $type_id
 * @property int $prev_status_id
 * @property int $status_id
 * @property int $parent_id
 * @property int $content_id
 * @property int $process_template_id
 * @property int $department_id
 * @property ?int $document_hierarchy_id
 * @property mixed $documents_hierarchy
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \App\Modules\Departments\Models\Department $department
 * @property StatusModel $prevStatus
 * @property StatusModel $status
 * @property Contents $contents
 * @property Participant $initiator
 * @property ?Participant $signatory
 * @property \Illuminate\Support\Collection $receivers
 * @property \Illuminate\Support\Collection $observers
 * @property \Illuminate\Support\Collection $mainFiles
 * @property \Illuminate\Support\Collection $additionalFiles
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
class Esz extends Model
{
	protected $table = 'l_esz';
	protected $with = [
		'type',
		'status',
		'contents',
		'initiator',
		'signatory',
		'receivers',
		'observers',
		'mainFiles',
		'additionalFiles',
		'history',
		'processHistory',
	];
	protected $appends = ['theme', 'parent_document', 'hierarchy', 'documents_hierarchy'];
	protected $hidden = ['documentHierarchy', 'theme_title'];

	public function type(): BelongsTo
	{
		return $this->belongsTo(DocumentType::class);
	}

	public function prevStatus(): BelongsTo
	{
		return $this->belongsTo(StatusModel::class);
	}

	public function status(): BelongsTo
	{
		return $this->belongsTo(StatusModel::class);
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
			->hasMany(EszFile::class)
			->where('type_id', FileType::MAIN);
	}

	public function additionalFiles(): HasMany
	{
		return $this
			->hasMany(EszFile::class)
			->where('type_id', FileType::ADDITIONAL);
	}

	public function department(): BelongsTo
	{
		return $this->belongsTo(Department::class, 'department_id');
	}

	public function initiator(): HasOne
	{
		return $this
			->hasOne(Participant::class)
			->where('type_id', ParticipantType::INITIATOR);
	}

	public function signatory(): HasOne
	{
		return $this
			->hasOne(Participant::class)
			->where('type_id', ParticipantType::SIGNATORY)
			->whereHas('user');
	}

	public function receivers(): HasMany
	{
		return $this
			->hasMany(Participant::class)
			->where('type_id', ParticipantType::RECEIVERS);
	}

	public function observers(): HasMany
	{
		return $this
			->hasMany(Participant::class)
			->where('type_id', ParticipantType::OBSERVERS);
	}

	public function participants(): HasMany
	{
		return $this->hasMany(Participant::class);
	}

	public function history(): HasMany
	{
		return $this->hasMany(History::class);
	}

	public function processHistory(): HasMany
	{
		return $this->hasMany(ProcessHistory::class);
	}

	public function templateDocument(): BelongsTo
	{
		return $this->belongsTo(DocumentTemplate::class, 'tmp_doc_id')->with('children');
	}

	public function getThemeAttribute(): ?string
	{
		return $this->templateDocument ? $this->templateDocument->title : $this->theme_title;
	}

	public function commonDocument(): HasOne
	{
		return $this->hasOne(Document::class, 'id', 'common_document_id');
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

		$hierarchy = DocumentHierarchyTree::firstWhere('document_id', $start_document_id);

		if (!$hierarchy) {
			return collect([]);
		}

		return collect([$hierarchy]);
	}

	public function getDocumentsHierarchyAttribute()
	{
		return $this->document_hierarchy_id ? DocumentsHierarchyFacade::getHierarchy($this->document_hierarchy_id) : null;
	}

	public function isPreparation(): bool
	{
		return $this->status_id === Status::PREPARATION;
	}

	public function isFix(): bool
	{
		return $this->status_id === Status::FIX;
	}

	public function isCoordination(): bool
	{
		return $this->status_id === Status::COORDINATION;
	}

	public function isFixSigning(): bool
	{
		return $this->status_id === Status::FIX_SIGNING;
	}

	public function isSigning(): bool
	{
		return $this->status_id === Status::SIGNING;
	}

	public function isFixResolution(): bool
	{
		return $this->status_id === Status::FIX_RESOLUTION;
	}

	public function isResolution(): bool
	{
		return $this->status_id === Status::RESOLUTION;
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