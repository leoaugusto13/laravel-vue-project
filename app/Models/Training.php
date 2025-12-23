<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_name',
        'year',
        'status',
        'directorate_id',
        'location_id',
        'venue',
        'training_type_id',
        'target_audience_id',
        'modality_id',
        'start_date',
        'workload'
    ];

    public function directorate()
    {
        return $this->belongsTo(Directorate::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function modality()
    {
        return $this->belongsTo(Modality::class);
    }

    public function trainingType()
    {
        return $this->belongsTo(TrainingType::class);
    }

    public function targetAudience()
    {
        return $this->belongsTo(TargetAudience::class);
    }

    public function strategies()
    {
        return $this->belongsToMany(Strategy::class, 'strategy_training');
    }
}
