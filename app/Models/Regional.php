<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'state_id', 'active'];
    protected $with = ['state'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
