<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    protected $table = 'followings';
    protected $fillable = ['follower_id', 'following'];
}
