<?php
namespace SED\Documents\Common\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Document extends Model
{
    protected $table = 'l_sed_documents';
    protected $fillable = [
        'document_id',
        'number',
        'type_id',
        'theme',
        'executor_id',
        'status_title',
    ];

    public function type(): HasOne
    {
        return $this->hasOne(DocumentType::class, 'id', 'type_id');
    }

    public function executor(): HasOne
    {
        return $this->hasOne(User::class, 'ID', 'executor_id');
    }
}