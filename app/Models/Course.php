<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use App\Models\User;
use App\Models\Attendance;

class Course extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'courses';

    protected $fillable = [
        'name',
        'code',
        'lecturer_id',
        'student_ids',
        'schedule_details',
        'semester',
    ];

    protected $casts = [
        'student_ids' => 'array',
        'schedule_details' => 'array',
    ];

    public function lecturer()
    {
        // Quan hệ lecturer là user với khóa ngoại 'lecturer_id'
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function students()
{
    return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id');
}
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'course_id');
    }
}
