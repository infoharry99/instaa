<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'lead_source_id',
        'business_name',
        'instagram_username',
        'instagram_profile_url',
        'profile_picture_url',
        'business_description',
        'category',
        'city',
        'state',
        'country',
        'location_address',
        'official_website',
        'public_email',
        'public_phone',
        'contact_person',
        'status',
        'priority',
        'source_type',
        'collected_at',
        'last_verified_at',
        'metadata',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(LeadContact::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }

    public function communicationLogs(): HasMany
    {
        return $this->hasMany(CommunicationLog::class)->latest();
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class)->orderBy('due_at', 'asc');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(LeadTag::class, 'lead_tag_assignments', 'business_lead_id', 'lead_tag_id');
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(LeadList::class, 'lead_list_items', 'business_lead_id', 'lead_list_id')
            ->withTimestamps();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LeadAssignment::class)->latest();
    }

    public function currentAssignment(): HasOne
    {
        return $this->hasOne(LeadAssignment::class)->latestOfMany();
    }
}
