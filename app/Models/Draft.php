<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Recipe
 */
class Draft extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id', 'resource_type'
    ];
}
