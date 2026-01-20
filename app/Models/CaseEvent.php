<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaseEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'legal_case_id',
        'actor_id',
        'actor_role',
        'event',
        'status_from',
        'status_to',
        'note',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function legalCase()
    {
        return $this->belongsTo(LegalCase::class, 'legal_case_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
