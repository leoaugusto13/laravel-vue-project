<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationForm extends Model
{
    protected $fillable = ['training_id', 'title', 'description', 'published'];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function fields()
    {
        return $this->hasMany(RegistrationFormField::class)->orderBy('order');
    }

    public function submissions()
    {
        return $this->hasMany(RegistrationSubmission::class);
    }
}
