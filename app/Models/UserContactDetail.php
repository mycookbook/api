<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserContactDetail extends Model
{
    const VISIBILITY = 'public';

    /**
     * @var array<string>
     */
    protected $fillable = [
        'visibility',
        'user_id',
        'facebook',
        'twitter',
        'instagram',
        'skype',
        'office_address',
        'phone',
        'calendly',
        'skype',
        'website',
    ];

    protected $hidden = ['id', 'user_id'];

    protected $appends = ['is_public'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->visibility === self::VISIBILITY;
    }

    /**
     * @return bool
     */
    public function getIsPublicAttribute()
    {
        return $this->isPublic();
    }
}
