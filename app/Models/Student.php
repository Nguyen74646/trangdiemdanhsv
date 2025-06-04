<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Student extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'students';

    protected $fillable = [
        'student_code',
        'full_name',
        'class_id',
        'gender',
        'dob',
    ];
}
