<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function comments(): MorphToMany
    {
        return $this->morphToMany(Comment::class, "commentable");
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, "taggable");
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, "post_category_id");
    }

    protected function publishStatus(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value ? "Published" : "Drafted"
        );
    }

    protected function publishedAt(): Attribute
    {
        return Attribute::make(get: fn ($value) => $value ?? "-");
    }
}
