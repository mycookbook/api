<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Recipe
 */
class Draft extends Model
{
    use HasFactory;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'resource_id', 'resource_type'
    ];
}
