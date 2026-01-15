<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationFormField extends Model
{
    protected $fillable = [
        'registration_form_id',
        'type',
        'label',
        'placeholder',
        'required',
        'options',
        'order'
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(RegistrationForm::class, 'registration_form_id');
    }
}
