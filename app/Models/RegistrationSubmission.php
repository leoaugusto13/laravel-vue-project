<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationSubmission extends Model
{
    protected $fillable = [
        'registration_form_id',
        'user_id',
        'guest_name',
        'guest_email',
        'answers'
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(RegistrationForm::class, 'registration_form_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
