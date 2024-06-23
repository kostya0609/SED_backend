<?php
namespace SED\Documents\Directive\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $title
 */
class StatusModel extends Model
{
	protected $table = 'l_directive_statuses';
	public $timestamps = false;
}