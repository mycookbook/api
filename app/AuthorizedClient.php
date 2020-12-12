<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthorizedClient extends Model
{
	protected $table= "authorized_clients";
	protected $fillable = ['api_key', 'client_secret', 'passphrase'];
}