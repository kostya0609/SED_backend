<?php

namespace App\Modules\SED\DocumentRoutes\Features\TemplatePartitions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use SED\Common\Models\User;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;

/**
 * @property string $title
 * @property int $route_id
 * @property int $creator_id
 * @property int $last_editor_id
 * @property int $parent_template_id
 */
class TemplatePartition extends Model
{
    protected $table = 'l_route_template_partitions';

    protected $with = [
        'creator',
        'lastEditor',
        'parentTemplate:id,title,type_id'
    ];

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(DocumentTemplate::class,
            'l_route_tmp_doc_relations',
            'parent_template_id',
            'child_template_id')
            ->withPivot(['id', 'root_template_id', 'parent_template_type', 'child_template_type'])
            ->wherePivot('child_template_type','=','template')
            ->wherePivot('parent_template_type','=','partition');
    }

    public function childrenTemplatePartitions(): BelongsToMany
    {
        return $this->belongsToMany(TemplatePartition::class,
            'l_route_tmp_doc_relations',
            'parent_template_id',
            'child_template_id')
            ->withPivot(['id', 'root_template_id', 'parent_template_type', 'child_template_type'])
            ->wherePivot('child_template_type','=','partition')
            ->wherePivot('parent_template_type','=','partition');

    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(DocumentTemplate::class,
            'l_route_tmp_doc_relations',
            'child_template_id',
            'parent_template_id')
            ->withPivot(['id', 'root_template_id','parent_template_type','child_template_type'])
            ->wherePivot('child_template_type','=','template');
    }

    public function parentTemplate(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'parent_template_id', 'id');
    }

    public function setBranchId(?int $branch_id = null): void
    {
        $this->setAttribute('branch_id', $branch_id);
    }

    public function setRootTemplateId(int $root_template_id)
    {
        $this->setAttribute('root_template_id', $root_template_id);
    }

    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'ID', 'creator_id');
    }

    public function lastEditor(): HasOne
    {
        return $this->hasOne(User::class, 'ID', 'last_editor_id');
    }
}
