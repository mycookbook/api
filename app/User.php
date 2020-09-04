<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * User Model
 */
class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    JWTSubject
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'following', 'followers', 'name_slug'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'id'
    ];

    /**
     * A user has many recipes
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipes()
    {
        return $this->hasMany('App\Recipe', 'user_id');
    }

	/**
	 * A user can be subscribed to multiple cookbooks
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
    public function cookbooks()
    {
        return $this->belongsToMany('App\Cookbook', 'cookbook_user');
    }

    /**
     * JWT Identifier
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Custom claims
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Append links attribute.
     *
     * @var array
     */
    protected $appends = [
    	'contributions',
		'_links'
	];

    /**
     * Set attributes links
     *
     * @return array
     */
    public function getLinksAttribute()
    {
        return [
            'self' => app()
                ->make('url')
                ->to("api/v1/users/{$this->attributes['name_slug']}")
        ];
    }

	/**
	 * Compute total nos of contributions made by this user
	 * cookbooks and recipes
	 *
	 * @return int
	 */
    public function getContributionsAttribute()
	{
		$cookbooks = $this->cookbooks()->count();
		$recipes = $this->recipes()->count();

		return $cookbooks + $recipes;
	}

	/**
	 * Set attribute created at
	 *
	 * @return string
	 */
    public function getCreatedAtAttribute()
	{
		$year = Carbon::parse($this->attributes['created_at'])->year;
		$month = Carbon::parse($this->attributes['created_at'])->month;

		return Carbon::createFromDate($year, $month)->format('F Y');
	}

	/**
	 * Get the User name_slug
	 *
	 * @return string
	 */
    public function getSlug()
	{
		return $this->name_slug;
	}
}
