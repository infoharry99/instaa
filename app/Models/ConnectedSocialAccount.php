<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectedSocialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'platform',
        'account_id',
        'username',
        'name',
        'profile_picture_url',
        'account_type',
        'access_token',
        'token_expires_at',
        'is_connected',
        'scopes',
        'meta_data',
    ];

    protected $casts = [
        'is_connected' => 'boolean',
        'token_expires_at' => 'datetime',
        'scopes' => 'array',
        'meta_data' => 'array',
    ];

    protected $hidden = [
        'access_token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
