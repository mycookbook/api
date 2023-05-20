<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    /**
     * @var array<string>
     */
    protected $fillable = ['user_id', 'token', 'is_verified'];

    public $timestamps = false;
}
