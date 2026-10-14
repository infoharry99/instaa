<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'domain',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'country',
        'logo_url',
        'plan',
        'max_leads_quota',
        'leads_used_count',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'max_leads_quota' => 'integer',
        'leads_used_count' => 'integer',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function businessLeads(): HasMany
    {
        return $this->hasMany(BusinessLead::class);
    }

    public function connectedSocialAccounts(): HasMany
    {
        return $this->hasMany(ConnectedSocialAccount::class);
    }

    public function leadLists(): HasMany
    {
        return $this->hasMany(LeadList::class);
    }

    public function leadTags(): HasMany
    {
        return $this->hasMany(LeadTag::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }
}
