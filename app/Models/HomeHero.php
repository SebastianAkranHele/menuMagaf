<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeHero extends Model
{
    use HasFactory;

    protected $table = 'home_hero';

    protected $fillable = [
        'title',
        'subtitle',
        'background_image',
        'social_links',
        'profile_title',
        'profile_subtitle',
        'profile_image',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];
}
