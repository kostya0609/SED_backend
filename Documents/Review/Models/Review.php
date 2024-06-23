<?php
namespace SED\Documents\Review\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Departments\Models\Department;
use SED\Documents\Review\Enums\{ParticipantType, FileType, Status};
use SED\Documents\Common\Models\{DocumentType, DocumentTheme};
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany, HasOne, BelongsTo};

/**
 * @property int $id
 * @property string $number
 * @property int $type_id
 * @property int $status_id
 * @property int $theme_id
 * @property int $parent_id
 * @property int $content_id
 * @property int $process_template_id
 * @property int $department_id
 * @property \App\Modules\Departments\Models\Department $department
 * @property Participant $responsible
 * @property \Illuminate\Support\Collection $receivers
 * @property StatusModel $status
 * @property DocumentTheme $theme
 * @property Contents $contents
 */
class Review extends Model
{
	protected $table = 'l_review';
	protected $with = ['theme','type','status','contents','department','mainFiles','responsible','receivers','history',	'processHistory',];

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

    public function theme(): BelongsTo
    {
        return $this->belongsTo(DocumentTheme::class);
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

	public function responsible(): HasOne
	{
		return $this
			->hasOne(Participant::class, 'review_id', 'id')
			->where('type_id', ParticipantType::RESPONSIBLE);
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

}
