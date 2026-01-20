<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LegalCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'assigned_lawyer_id',
        'case_no',
        'title',
        'category',
        'type',
        'status',
        'summary',
        'submitted_at',
        'resolved_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'assigned_lawyer_id');
    }

    public function files()
    {
        return $this->hasMany(\App\Models\CaseFile::class, 'legal_case_id');
    }

    public function events()
    {
        return $this->hasMany(\App\Models\CaseEvent::class, 'legal_case_id')->latest();
    }

    public function applications()
    {
        return $this->hasMany(\App\Models\CaseApplication::class, 'legal_case_id');
    }
}
