<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'provider_type',
        'description',
        'is_active',
        'api_endpoint',
        'rate_limit_per_minute',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rate_limit_per_minute' => 'integer',
        'settings' => 'array',
    ];

    public function businessLeads(): HasMany
    {
        return $this->hasMany(BusinessLead::class);
    }
}
