<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'expires_at',
        'revoked_at'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}
