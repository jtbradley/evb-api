<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\ProjectDeleted;

class Project extends Model
{
    protected $primaryKey = 'trello_id';
    public $incrementing = false;
    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
