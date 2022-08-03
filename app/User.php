<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\CookbookUserMustVerifyEmail;
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
    use Authenticatable, Authorizable, CookbookUserMustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'following', 'followers', 'name_slug', 'email_verified'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'id', 'pivot'
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
	 * User has one contact detail
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
    public function contact()
	{
		return $this->hasOne('App\UserContactDetail');
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
    public function getJWTCustomClaims(): array
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
		'is_verified',
		'contact_detail',
        'drafts',
	];

	/**
	 * Compute total nos of contributions made by this user
	 * cookbooks and recipes
	 *
	 * @return array
	 */
    public function getContributionsAttribute(): array
	{
		$cookbooks = $this->cookbooks()->count();
		$recipes = $this->recipes()->count();

		return [
			'cookbooks' => $cookbooks,
			'recipes' => $recipes,
			'total' => $cookbooks + $recipes
		];
	}

	/**
	 * Set attribute created at
	 *
	 * @return string
	 */
    public function getCreatedAtAttribute(): string
	{
		$year = Carbon::parse($this->attributes['created_at'])->year;
		$month = Carbon::parse($this->attributes['created_at'])->month;

		return Carbon::createFromDate($year, $month)->format('F Y');
	}

	/**
	 * Show user email verification status
	 *
	 * @return bool
	 */
	public function getIsVerifiedAttribute()
	{
		$entity = $this->email_verification()->get()->first();
		if (is_null($entity) || is_null($entity->is_verified)) {
			return false;
		}
		return true;
	}

	/**
	 * Get user contact detail
	 *
	 * @return mixed
	 */
	public function getContactDetailAttribute()
	{
		return $this->contact()->get()->first();
	}

	/**
	 * Get the User name_slug
	 *
	 * @return string
	 */
    public function getSlug(): string
	{
		return $this->name_slug;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function email_verification()
	{
		return $this->hasOne('App\EmailVerification');
	}

	public function isVerified()
	{
		return $this->email_verified;
	}

    /**
     * @return string
     */
    public function getFollowersAttribute()
    {
        if ($this->attributes['followers'] <= 10000) {
            return strval($this->attributes['followers']);
        }

        return '10K+';
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getDraftsAttribute(): \Illuminate\Support\Collection
    {
        return collect([]);
    }
}
