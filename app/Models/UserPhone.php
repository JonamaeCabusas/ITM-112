<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserPhone extends Model
{
    protected $fillable = ['name'];

    /** A phone owner kay naay naay assciated nga phone number. */
    public function phone(): HasOne
    {
        return $this->hasOne(Phone::class);
    }
}