<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'author_name',
        'author_email',
        'content',
        'approved',
    ];

    protected function casts(): array
    {
        return [
            'approved' => 'boolean',
        ];
    }

    /* ── Relations ── */

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /* ── Scopes ── */

    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('approved', false);
    }
}
