<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'status',
        'featured',
        'views',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'featured'     => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /* ── Boot ── */

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if ($post->status === 'published' && is_null($post->published_at)) {
                $post->published_at = now();
            }
        });

        static::updating(function (Post $post) {
            // Auto-set published_at when first published
            if ($post->isDirty('status') && $post->status === 'published' && is_null($post->published_at)) {
                $post->published_at = now();
            }
            // Clear published_at when reverted to draft
            if ($post->isDirty('status') && $post->status === 'draft') {
                $post->published_at = null;
            }
        });
    }

    /* ── Scopes ── */

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /* ── Relations ── */

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->where('approved', true);
    }

    /* ── Accessors ── */

    /**
     * Estimated reading time in minutes (200 words/min average).
     */
    public function getReadingTimeAttribute(): int
    {
        $words = Str::wordCount(strip_tags($this->content));

        return max(1, (int) ceil($words / 200));
    }
}
