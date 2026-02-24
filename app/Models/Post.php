<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MoonShine\ChangeLog\Traits\HasChangeLog;

class Post extends Model
{
    use HasFactory, HasChangeLog, SoftDeletes;

    protected $fillable = [
        'post_category_id',
        'title',
        'slug',
        'content',
        'image',
        'views',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'views' => 'integer',
    ];

    public function postCategory(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class);
    }
}
