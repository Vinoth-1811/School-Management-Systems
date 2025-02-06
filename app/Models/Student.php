<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'department',
        'address',
    ];

    // In Student.php
    public function getDepartment()
    {
        return $this->belongsTo(Department::class, 'department', 'id');
    }

}
