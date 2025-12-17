<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directorate extends Model
{
    protected $fillable = ['description', 'acronym', 'status'];

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }
}
