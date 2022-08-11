<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $fillable = ['user_id', 'token', 'is_verified'];

    public $timestamps = false;
}
