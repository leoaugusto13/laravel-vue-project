<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'year',
        'status',
        'directorate_id'
    ];

    public function directorate()
    {
        return $this->belongsTo(Directorate::class);
    }
}
