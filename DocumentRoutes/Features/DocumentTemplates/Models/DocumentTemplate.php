<?php
namespace SED\DocumentRoutes\Features\DocumentTemplates\Models;

use App\Modules\SED\DocumentRoutes\Features\TemplatePartitions\Models\TemplatePartition;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use SED\Common\Models\User;
use Illuminate\Database\Eloquent\Model;
use SED\DocumentRoutes\SEDApprovalRoute;
use SED\Documents\Common\Models\DocumentType;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SED\DocumentRoutes\Features\Routes\Models\Route;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SED\DocumentRoutes\Features\Automation\Models\SettingValue;


/**
 * @property int $id
 * @property string $title
 * @property object $data
 * @property bool $is_start
 * @property bool $is_active
 * @property int $creator_id
 * @property int $last_editor_id
 * @property int $parent_id
 * @property int $route_id
 * @property int $type_id
 * @property ?int $branch_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property ?string $requirements
 *
 * @property-read \SED\Documents\Common\Models\DocumentType $type
 * @property-read \SED\Common\Models\User $creator
 * @property-read \SED\Common\Models\User $lastEditor
 * @property-read \Illuminate\Database\Eloquent\Collection|\SED\DocumentRoutes\SEDApprovalRoute[] $approvalRoutes
 * @property-read \Illuminate\Database\Eloquent\Collection|\SED\DocumentRoutes\Features\Automation\Models\SettingValue[] $settings
 * @property-read \Illuminate\Database\Eloquent\Collection|\SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate[] $children
 * @property-read \SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate|null $parent
 * @property-read \SED\DocumentRoutes\Features\Routes\Models\Route|null $route
 * @property-read bool $check_template_usage
 * @property TemplatePartition $childrenTemplatePartitions
 */
class DocumentTemplate extends Model
{
	protected $table = 'l_route_tmp_docs';

	protected $casts = [
		'data' => 'object',
		'is_start' => 'boolean',
		'is_active' => 'boolean',
	];

	protected $with = [
		'type',
		'creator',
		'lastEditor',
		'approvalRoutes',
		'settings',
		'parents',
	];

	protected $appends = ['check_template_usage'];

	public function children(): BelongsToMany
	{
		return $this->belongsToMany(DocumentTemplate::class,
            'l_route_tmp_doc_relations',
            'parent_template_id',
            'child_template_id')
            ->withPivot(['id', 'root_template_id', 'parent_template_type', 'child_template_type'])
            ->wherePivot('child_template_type','=','template')
            ->wherePivot('parent_template_type','=','template');

    }

    public function childrenTemplatePartitions(): BelongsToMany
    {
        return $this->belongsToMany(TemplatePartition::class,
            'l_route_tmp_doc_relations',
            'parent_template_id',
            'child_template_id')
            ->withPivot(['id', 'root_template_id', 'parent_template_type', 'child_template_type'])
            ->wherePivot('child_template_type','=','partition')
            ->wherePivot('parent_template_type','=','template');

    }

	public function parents(): BelongsToMany
	{
		return $this->belongsToMany(DocumentTemplate::class,
            'l_route_tmp_doc_relations',
            'child_template_id',
            'parent_template_id')
            ->withPivot(['id', 'root_template_id','parent_template_type','child_template_type']);
//            ->wherePivot('child_template_type','=','template')
//            ->wherePivot('parent_template_type','=','template');
	}

	public function route(): BelongsTo
	{
		return $this->belongsTo(Route::class);
	}

	public function type(): BelongsTo
	{
		return $this->belongsTo(DocumentType::class);
	}

	public function creator(): HasOne
	{
		return $this->hasOne(User::class, 'ID', 'creator_id');
	}

	public function lastEditor(): HasOne
	{
		return $this->hasOne(User::class, 'ID', 'last_editor_id');
	}

	public function approvalRoutes(): HasMany
	{
		return $this->hasMany(SEDApprovalRoute::class, 'tmp_doc_id', 'id');
	}

	public function settings(): HasMany
	{
		return $this->hasMany(SettingValue::class, 'tmp_doc_id', 'id');
	}

	public function getCheckTemplateUsageAttribute(): bool
	{
		return \DB::table(\DB::raw("(SELECT tmp_doc_id FROM l_esz UNION SELECT tmp_doc_id FROM l_directive UNION SELECT tmp_doc_id FROM l_review) as q"))
			->where('q.tmp_doc_id', $this->id)
			->exists();
	}

	public function setBranchId(?int $branch_id = null): void
	{
		$this->setAttribute('branch_id', $branch_id);
	}

	public function setRootTemplateId(int $root_template_id)
	{
		$this->setAttribute('root_template_id', $root_template_id);
	}

	public function isDirective(): bool
	{
		return $this->type_id === \SED\Documents\Common\Enums\DocumentType::DIRECTIVE;
	}

	public function isEsz(): bool
	{
		return $this->type_id === \SED\Documents\Common\Enums\DocumentType::ESZ;
	}

	public function isReview(): bool
	{
		return $this->type_id === \SED\Documents\Common\Enums\DocumentType::REVIEW;
	}
}
