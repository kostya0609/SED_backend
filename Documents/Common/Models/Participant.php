<?php
namespace SED\Documents\Common\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
	protected $table = 'l_sed_document_participants';
	protected $fillable = ['user_id', 'document_id'];
	public $timestamps = false;
}