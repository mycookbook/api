<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorizedClient extends Model
{
    protected $table = 'authorized_clients';

    /**
     * @var array<string>
     */
    protected $fillable = ['api_key', 'client_secret', 'passphrase'];
}
