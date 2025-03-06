<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'token',
        'started_at',
        'expires_at',
        'completed_at',
        'question_count'
    ];
    
    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function result()
    {
        return $this->hasOne(AssessmentResult::class);
    }
}

class AssessmentQuestion extends Model
{
    protected $fillable = [
        'type',
        'question',
        'correct_answer',
        'options',
        'difficulty',
        'explanation'
    ];
    
    protected $casts = [
        'options' => 'array'
    ];
}

class AssessmentResult extends Model
{
    protected $fillable = [
        'user_id',
        'assessment_id',
        'assessment_type',
        'score',
        'max_score',
        'percentage',
        'passed',
        'time_taken',
        'completed_at'
    ];
    
    protected $casts = [
        'percentage' => 'float',
        'passed' => 'boolean',
        'completed_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}