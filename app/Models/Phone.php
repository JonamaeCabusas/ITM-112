<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Phone extends Model
{
    protected $fillable = ['user_phone_id', 'phone_number'];

    /** The phone record belongs to its phone owner. */
    public function userPhone(): BelongsTo
    {
        return $this->belongsTo(UserPhone::class);
    }
}