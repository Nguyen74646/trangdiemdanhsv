<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Course;
use App\Models\Attendance;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable, HasApiTokens, Notifiable;

    // Kết nối MongoDB và collection users
    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // admin, giangvien, sinhvien
        'student_id_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function teachingCourses()
    {
        if ($this->role === 'giangvien') {
            return $this->hasMany(Course::class, 'lecturer_id');
        }
        return $this->newQuery()->whereNull('_id');
    }

    public function enrolledCourses()
    {
        if ($this->role === 'sinhvien') {
            return Course::where('student_ids', $this->getKey())->get();
        }
        return collect();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }
public function courses()
{
    return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id');
}

    public function markedAttendances()
    {
        if ($this->role === 'giangvien') {
            return $this->hasMany(Attendance::class, 'marked_by_id');
        }
        return $this->newQuery()->whereNull('_id');
    }
}
