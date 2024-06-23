<?php
namespace SED\Documents\Directive\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 */
class ParticipantTypeModel extends Model
{
	protected $table = 'l_directive_participant_types';
	public $timestamps = false;
}