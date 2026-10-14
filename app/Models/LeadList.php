<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadList extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'description',
        'color',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LeadListItem::class);
    }

    public function leads(): BelongsToMany
    {
        return $this->belongsToMany(BusinessLead::class, 'lead_list_items', 'lead_list_id', 'business_lead_id')
            ->withTimestamps();
    }
}
