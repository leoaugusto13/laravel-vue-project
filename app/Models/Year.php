<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    protected $fillable = [
        'year',
        'is_closed'
    ];

    protected $casts = [
        'is_closed' => 'boolean',
    ];
}
