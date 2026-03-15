<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $fillable = [
        'id',
        'name',
        'owner_id'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_user')
            ->withPivot('role')
            ->withTimestamps();
    }


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
