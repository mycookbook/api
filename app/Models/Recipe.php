<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Recipe
 */
class Recipe extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'imgUrl',
        'ingredients',
        'description',
        'user_id',
        'cookbook_id',
        'summary',
        'nutritional_detail',
        'slug',
        'calorie_count',
        'cook_time',
        'prep_time',
        'tags',
        'claps',
        'course',
        'cuisine'
    ];

    protected $hidden = ['user_id'];

    protected $casts = [
        'cook_time' => 'datetime:H:i:s',
        'prep_time' => 'datetime:H:i:s',
        'tags' => 'array'
    ];

    /**
     * Append custom attributes
     *
     * @var array
     */
    protected $appends = [
        'total_time',
        'varieties_count',
        '_links',
        'author',
        'submission_date',
        'comments',
        'servings',
        'prep_time',
        'is_draft'
    ];

    /**
     * A recipe belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A recipe belongs to a cookbook
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cookbook(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\Cookbook');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\RecipeVariation');
    }

    /**
     * Set attributes links
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getLinksAttribute(): array
    {
        return [
            'self' => app()
                ->make('url')
                ->to("api/v1/recipes/{$this->attributes['slug']}")
        ];
    }

    /**
     * @return string
     */
    public function getTotalTimeAttribute(): string
    {
        $cook_time = strtotime(Carbon::parse($this->attributes['cook_time']));
        $prep_time = strtotime(Carbon::parse($this->attributes['prep_time']));
        $total_time = $cook_time + $prep_time;

        return Carbon::createFromTimestamp($total_time);
        //		return CarbonInterval::createFromFormat('H:i:s', $total_time)->forHumans();
    }

    /**
     * Compute Recipe variations count
     *
     * @return int
     */
    public function getVarietiesCountAttribute(): int
    {
        return 0;
    }

    /**
     * Set Attribute Cook Time
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getCookTimeAttribute(): string
    {
        $dt = Carbon::parse($this->attributes['cook_time']);

        return $dt->diffForHumans();
    }

    /**
     * Set Attribute prep Time
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getPrepTimeAttribute(): string
    {
        $dt = Carbon::parse($this->attributes['prep_time']);

        return $dt->diffForHumans();
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getSubmissionDateAttribute(): string
    {
        $dt = Carbon::parse($this->attributes['created_at']);

        return $dt->diffForHumans();
    }

    /**
     * @return mixed
     */
    public function getAuthorAttribute()
    {
        return $this->user()->get()->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RecipeTag::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCommentsAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->hasMany(Comment::class)->get();
    }

    /**
     * @return string
     */
    public function getServingsAttribute()
    {
        return "servings";
    }

    /**
     * Set attribute created at
     *
     * @return string
     */
    public function getCreatedAtAttribute(): string
    {
        $day = Carbon::parse($this->attributes['created_at'])->day;
        $year = Carbon::parse($this->attributes['created_at'])->year;
        $month = Carbon::parse($this->attributes['created_at'])->month;

        return Carbon::createFromDate($year, $month, $day)->format('F m Y');
    }

    /**
     * @return bool
     */
    public function getIsDraftAttribute(): bool
    {
        $draft = Draft::where(["resource_id" => $this->getKey()])->first();

        return $draft instanceof Draft;
    }
}
