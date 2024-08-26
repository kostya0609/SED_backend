<?php
namespace SED\Documents\Common\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentHistory extends Model
{
	protected $table = 'l_sed_document_history';
	protected $primaryKey = null;
	public $incrementing = false;
	protected $fillable = [
		'document_id',
		'type_id',
		'number',
	];
}