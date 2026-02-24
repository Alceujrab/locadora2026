<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MoonShine\ChangeLog\Traits\HasChangeLog;

class SeoMetadata extends Model
{
    use HasFactory, HasChangeLog;

    protected $fillable = [
        'url',
        'title',
        'description',
        'keywords',
        'og_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
