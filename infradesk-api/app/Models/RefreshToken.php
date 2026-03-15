<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
   protected $fillable = [
        'id',
        'user_id',
        'token_hash',
        'expires_at',
        'revoked_at'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}
