<?php

namespace SED\DocumentRoutes\Features\DocumentTemplates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use SED\DocumentRoutes\Features\Routes\Models\Route;
use SED\Documents\Common\Models\DocumentType;
use SED\Common\Models\User;


/**
 * @property int $id
 * @property int $type_id
 * @property bool $is_start
 * @property bool $is_active
 */
class DocumentTemplate extends Model
{
    protected $table = 'l_route_tmp_docs';

    protected $casts = [
        'data' => 'array',
        'is_start' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $with = [
        'type',
        'creator',
        'lastEditor',   
        'children'
    ];

    public function children(): HasMany
    {
        return $this->hasMany(DocumentTemplate::class, 'parent_id');
    }
    
    public function parent():BelongsTo 
    {
        return $this->belongsTo(DocumentTemplate::class, 'parent_id');
    }
    
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
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
