<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'modality_id',
        'city_id',
        'regional_id',
        'uaitec',
        'ead'
    ];

    public function modality()
    {
        return $this->belongsTo(Modality::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }
}
