<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaseFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'legal_case_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function legalCase()
    {
        return $this->belongsTo(LegalCase::class, 'legal_case_id');
    }
}
