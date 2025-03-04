<?php
namespace SED\DocumentRoutes\Features\ApprovalRoutes\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\ApprovalRoutes\ApprovalRoute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $tmp_doc_id
 * @property int $approval_route_id
 * @property ApprovalRoute $approvalRoute
 */
class SEDApprovalRoute extends Model
{
	protected $table = 'l_route_approval_routes';
	protected $primaryKey = null;
	public $incrementing = false;
	public $timestamps = false;
	protected $with = ['approvalRoute'];
	protected $fillable = ['tmp_doc_id', 'approval_route_id'];

	public function approvalRoute(): BelongsTo
	{
		return $this
			->belongsTo(ApprovalRoute::class)
			->with(
				'processTemplate',
				fn($query) => $query->without(['stages', 'statuses', 'user', 'senderUser'])
			);
	}
}