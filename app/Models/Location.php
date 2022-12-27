<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /**
     * @var string $primaryKey
     */
    protected $primaryKey = "ip";

    /**
     * @var string $table
     */
    protected $table = "user_locations";

    /**
     * @var string[] $fillable
     */
    public $fillable = [
        'ip', 'user_id', 'country', 'city', 'timezone', 'device'
    ];

    /**
     * @return mixed
     */
    public function getUser()
    {
        return User::findOrFail($this->user_id);
    }
}