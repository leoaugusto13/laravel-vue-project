<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'state_id', 'active'];
    protected $with = ['state'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'city_training');
    }
}
