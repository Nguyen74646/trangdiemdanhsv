<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use App\Models\User;
use App\Models\Course;

class Attendance extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'attendances';

    protected $fillable = [
        'course_id',
        'student_id',
        'attendance_date',
        'session',
        'status',
        'marked_by_id',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'datetime:Y-m-d', // ISODate trong MongoDB
    ];

    // Accessor trả về đối tượng Course
    public function getCourseAttribute()
    {
        return Course::find($this->course_id);
    }

    // Accessor trả về đối tượng User là Student
    public function getStudentAttribute()
    {
        return User::find($this->student_id);
    }

    // Accessor trả về đối tượng User là Marker (người điểm danh)
    public function getMarkerAttribute()
    {
        return User::find($this->marked_by_id);
    }
}
