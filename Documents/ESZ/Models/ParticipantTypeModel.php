<?php
namespace SED\Documents\ESZ\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 */
class ParticipantTypeModel extends Model
{
	protected $table = 'l_esz_participant_types';
	public $timestamps = false;
}