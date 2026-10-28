<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedVerificationAttempt extends Model
{
    public $timestamps = false; // We only use created_at in this table
    
    protected $fillable = [
        'phone',
        'ip_address',
        'created_at',
    ];
}