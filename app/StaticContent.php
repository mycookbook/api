<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaticContent extends Model
{
    protected $fillable = ['title', 'contents'];
}
