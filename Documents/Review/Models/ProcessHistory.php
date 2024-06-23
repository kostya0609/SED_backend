<?php
namespace SED\Documents\Review\Models;

use App\Modules\File\Models\File;
use SED\Documents\Common\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class ProcessHistory extends Model
{
    protected $table = 'l_review_process_history';
    protected $fillable = [
        'event',
        'comment',
        'user_id',
        'review_id',
    ];
    protected $with = ['user', 'files'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'ID', 'user_id');
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'l_review_process_history_files', 'history_id', 'file_id');
    }
}
