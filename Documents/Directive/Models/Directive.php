<?php
namespace SED\Documents\Directive\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Departments\Models\Department;
use SED\Documents\Common\Models\{DocumentType, DocumentTheme};
use SED\Documents\Directive\Enums\{ParticipantType, FileType, Status};
use Illuminate\Database\Eloquent\Relations\{HasMany, HasOne, BelongsTo};

/**
 * @property int $id
 * @property string $number
 * @property int $type_id
 * @property int $status_id
 * @property int $parent_id
 * @property int $content_id
 * @property int $theme_id
 * @property string $executed_at
 * @property int $process_template_id
 * @property int $department_id
 * @property \App\Modules\Departments\Models\Department $department
 * @property StatusModel $status
 * @property DocumentTheme $theme
 * @property Contents $contents
 * @property Participant $creator
 * @property Participant $author
 * @property \Illuminate\Support\Collection $executors
 * @property \Illuminate\Support\Collection $controllers
 * @property \Illuminate\Support\Collection $observers
 * @property \Illuminate\Support\Collection $mainFiles
 */
class Directive extends Model
{
	protected $table = 'l_directive';
	protected $casts = [
		'executed_at' => 'datetime',
	];
	protected $with = [
		'theme',
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
	];

	public function type(): HasOne
	{
		return $this->hasOne(DocumentType::class, 'id', 'type_id');
	}

	public function status(): HasOne
	{
		return $this->hasOne(StatusModel::class, 'id', 'status_id');
	}

	public function theme(): BelongsTo
	{
		return $this->belongsTo(DocumentTheme::class);
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
}