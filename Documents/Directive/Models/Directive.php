<?php
namespace SED\Documents\Directive\Models;

use \Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use SED\DocumentRoutes\DocumentTemplate;
use \App\Modules\Departments\Models\Department;
use App\Modules\DocumentsHierarchy\DocumentsHierarchyFacade;
use SED\Documents\Directive\Enums\{ParticipantType, FileType, Status};
use Illuminate\Database\Eloquent\Relations\{HasMany, HasOne, BelongsTo};
use SED\Documents\Common\Models\{DocumentType, DocumentHierarchy, Document, DocumentHierarchyTree};

/**
 * @property int $id
 * @property string $number
 * @property int $type_id
 * @property int $status_id
 * @property int $parent_id
 * @property int $content_id
 * @property string $executed_at
 * @property int $process_template_id
 * @property int $department_id
 * @property string $execution_control_date
 * @property ?int $tmp_doc_id
 * @property ?int$root_tmp_id
 * @property ?int $document_hierarchy_id
 * @property \App\Modules\Departments\Models\Department $department
 * @property StatusModel $status
 * @property Contents $contents
 * @property Participant $creator
 * @property ?Participant $author
 * @property \Illuminate\Support\Collection $executors
 * @property \Illuminate\Support\Collection $controllers
 * @property \Illuminate\Support\Collection $observers
 * @property \Illuminate\Support\Collection $mainFiles
 * @property History $history
 * @property ProcessHistory $processHistory
 * @property DocumentType $type
 * @property string $theme_title
 * @property ?int $common_document_id
 * @property DocumentTemplate $templateDocument
 * @property ?string $theme
 * @property Document $commonDocument
 * @property DocumentHierarchy $documentHierarchy
 * @property DocumentHierarchy $parent_document
 * @property Collection $hierarchy
 */
class Directive extends Model
{
	protected $table = 'l_directive';
	protected $casts = [
		'executed_at' => 'datetime',
		'execution_control_date' => 'datetime',
	];
	protected $with = [
		'type',
		'status',
		'contents',
		'department',
		'creator',
		'author',
		'executors',
		'controllers',
		'observers',
		'mainFiles',
		'history',
		'processHistory',
		'templateDocument',
	];
	protected $appends = ['theme', 'parent_document', 'hierarchy', 'documents_hierarchy'];
	protected $hidden = ['documentHierarchy', 'theme_title'];
    /**
     * @var int|mixed|null
     */

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
		return $this->hasOne(Contents::class);
	}

	public function mainFiles(): HasMany
	{
		return $this
			->hasMany(DirectiveFile::class)
			->where('type_id', FileType::MAIN);
	}

	public function department(): HasOne
	{
		return $this->hasOne(Department::class, 'ID', 'department_id');
	}

	public function creator(): HasOne
	{
		return $this
			->hasOne(Participant::class, 'directive_id', 'id')
			->where('type_id', ParticipantType::CREATOR);
	}

	public function author(): HasOne
	{
		return $this
			->hasOne(Participant::class, 'directive_id', 'id')
			->where('type_id', ParticipantType::AUTHOR);
	}

	public function executors(): HasMany
	{
		return $this
			->hasMany(Participant::class)
			->where('type_id', ParticipantType::EXECUTORS);
	}

	public function controllers(): HasMany
	{
		return $this
			->hasMany(Participant::class)
			->where('type_id', ParticipantType::CONTROLLERS);
	}

	public function observers(): HasMany
	{
		return $this
			->hasMany(Participant::class)
			->where('type_id', ParticipantType::OBSERVERS);
	}

	public function history(): HasMany
	{
		return $this->hasMany(History::class, 'directive_id', 'id');
	}

	public function processHistory(): HasMany
	{
		return $this->hasMany(ProcessHistory::class, 'directive_id', 'id');
	}

	public function templateDocument(): BelongsTo
	{
		return $this->belongsTo(DocumentTemplate::class, 'tmp_doc_id');
	}

	public function hierarchy(): HasMany
	{
		return $this->hasMany(DocumentHierarchy::class, 'parent_document_id', 'common_document_id');
	}

	public function getThemeAttribute(): ?string
	{
		return $this->templateDocument ? $this->templateDocument->title : $this->theme_title;
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

	public function getDocumentsHierarchyAttribute()
	{
		return $this->document_hierarchy_id ? DocumentsHierarchyFacade::getHierarchy($this->document_hierarchy_id) : null;
	}

	public function isPreparation(): bool
	{
		return $this->status_id === Status::PREPARATION;
	}
	public function isExecutionChangeRequest(): bool
	{
		return $this->status_id === Status::EXECUTION_CHANGE_REQUEST;
	}

	public function isExecutionInWork(): bool
	{
		return $this->status_id === Status::EXECUTION_IN_WORK;
	}

	public function isExecutionControl(): bool
	{
		return $this->status_id === Status::EXECUTION_CONTROL;
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
