<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaticContent extends Model
{
    /**
     * @var array<string>
     */
    protected $fillable = ['title', 'contents'];
}
