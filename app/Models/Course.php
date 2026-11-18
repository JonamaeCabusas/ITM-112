<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = ['title'];

    /** A course can include many students through the course_student table. */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }
}
