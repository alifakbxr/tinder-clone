<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'age',
        'email',
        'password',
        'latitude',
        'longitude',
        'popular_notification_sent_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'popular_notification_sent_at' => 'datetime',
    ];

    /**
     * Get the pictures for the user.
     */
    public function pictures()
    {
        return $this->hasMany(UserPicture::class);
    }

    /**
     * Get the swipes performed by the user.
     */
    public function swipes()
    {
        return $this->hasMany(Swipe::class, 'swiper_id');
    }

    /**
     * Get the swipes received by the user.
     */
    public function swipesReceived()
    {
        return $this->hasMany(Swipe::class, 'swiped_id');
    }
}
