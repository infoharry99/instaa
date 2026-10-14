<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_list_id',
        'business_lead_id',
        'added_by_user_id',
    ];

    public function list(): BelongsTo
    {
        return $this->belongsTo(LeadList::class, 'lead_list_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(BusinessLead::class, 'business_lead_id');
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }
}
