<?php
namespace SED\Documents\Review\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 */
class ParticipantTypeModel extends Model
{
	protected $table = 'l_review_participant_types';
	public $timestamps = false;
}
