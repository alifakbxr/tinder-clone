<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Swipe extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'swiper_id',
        'swiped_id',
        'action',
    ];

    /**
     * Get the user who performed the swipe.
     */
    public function swiper()
    {
        return $this->belongsTo(User::class, 'swiper_id');
    }

    /**
     * Get the user who was swiped.
     */
    public function swiped()
    {
        return $this->belongsTo(User::class, 'swiped_id');
    }
}
