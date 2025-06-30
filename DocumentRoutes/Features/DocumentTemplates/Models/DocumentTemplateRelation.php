<?php
namespace SED\DocumentRoutes\Features\DocumentTemplates\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $parent_template_id
 * @property int $child_template_id
 * @property ?int $root_template_id
 */
class DocumentTemplateRelation extends Model
{
	protected $table = 'l_route_tmp_doc_relations';
	public $incrementing = false;
	protected $primaryKey = false;
	protected $fillable = ['id', 'parent_template_id', 'child_template_id',
        'root_template_id', 'parent_template_type', 'child_template_type'];
}
