<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CookbookUserMustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use CookbookUserMustVerifyEmail;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'following',
        'followers',
        'name_slug',
        'email_verified',
        'avatar',
        'pronouns',
        'about',
    ];

    protected $hidden = [
        'password', 'remember_token', 'id', 'pivot'
    ];

    /**
     * Append links attribute.
     *
     * @var array
     */
    protected $appends = [
        'contributions',
        'is_verified',
        'contact_detail',
        'resource_type',
        'onboarding'
    ];

    /**
     * A user has many recipes
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\Recipe', 'user_id');
    }

    /**
     * User has one contact detail
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function contact(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\Models\UserContactDetail');
    }

    /**
     * A user can be subscribed to multiple cookbooks
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cookbooks(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany('App\Models\Cookbook', 'cookbook_user');
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
            'total' => $cookbooks + $recipes,
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
    public function getIsVerifiedAttribute(): bool
    {
        $entity = $this->email_verification()->first();

        if (is_null($entity) || is_null($entity->is_verified)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getResourceTypeAttribute(): string
    {
        return 'user';
    }

    /**
     * Get user contact detail
     *
     * @return mixed
     */
    public function getContactDetailAttribute()
    {
        return $this->contact()->first();
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
    public function email_verification(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\Models\EmailVerification');
    }

    public function isVerified()
    {
        return $this->email_verified;
    }

    /**
     * @return string
     */
    public function getFollowersAttribute(): string
    {
        if ($this->attributes['followers'] <= 10000) {
            return strval($this->attributes['followers']);
        }

        return '10K+';
    }

    public function getOnboardingAttribute(): Collection
    {
        $feedback = collect(UserFeedback::where(['user_id' => $this->getKey(), 'type' => 'feedback'])->pluck('response')->toArray())->first();

        return collect(['likelihoodToShare' => $feedback]);
    }

    /**
     * @param $q
     * @param  array  $relationships
     * @param  array  $orWhereFields
     * @return mixed
     */
    public static function findWhere($q, array $relationships = [], array $orWhereFields = [])
    {
        $record = self::where(['id' => $q]);

        if ($relationships) {
            $record = $record->with($relationships);
        }

        if ($orWhereFields) {
            foreach ($orWhereFields as $orWhere) {
                $record = $record->orWhere($orWhere, $q);
            }
        }

        $record = $record->get();

        if ($record->isEmpty()) {
            throw new ModelNotFoundException('User record not found.');
        }

        return $record;
    }

    /**
     * @param $cookbookId
     * @return bool
     */
    public function ownCookbook($cookbookId)
    {
        $cookbook = Cookbook::findOrFail($cookbookId);

        return ($this->getKey() == $cookbook->user_id);
    }

    /**
     * @param $recipeId
     * @return bool
     */
    public function ownRecipe($recipeId)
    {
        $recipe = Recipe::findOrFail($recipeId);

        return ($this->getKey() == $recipe->user_id);
    }

    /**
     * This is reserved for strictly cookbook admin
     * @return bool
     */
    public function isSuper(): bool
    {
        return $this->hasRole('super');
    }

    /**
     * @return bool
     */
    public function isEarlyBird(): bool
    {
        $earlyBirds = [
            'a93c026e-dd03-4e99-98d9-a9d68a61b42c@tiktok.com',
            'test.user@cookbookshq.com'
        ];

        return in_array($this->email, $earlyBirds);
    }

    public function isAlreadyFollowing(User $user): bool
    {
        $followings = Following::where(['follower_id' => $this->getKey()])->pluck('following')->toArray();

        return in_array($user->getKey(), $followings);
    }

    private function hasRole(string $role)
    {
        return false;
    }

    public function getTikTokUser()
    {
        return DB::table('tiktok_users')->where(['user_id' => $this->getKey()])->first();
    }

    public function getTiktokVideosAttribute(): Collection
    {
        $tikTokUser = DB::table('tiktok_users')->where(['user_id' => $this->getKey()])->first();

        if ($tikTokUser === null) {
            return collect();
        }

        return collect(json_decode($tikTokUser->videos, true));
    }

    public function ownsComment(int $commentId)
    {
        return Comment::findOrFail($commentId)->user_id == $this->getKey();
    }
}
