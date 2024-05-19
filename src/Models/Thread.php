<?php

declare(strict_types=1);

namespace Appleton\Threads\Models;

use Database\Factories\ThreadFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property-read string $id
 * @property string $threaded_id
 * @property string $threaded_type
 * @property string $user_id
 * @property string $title
 * @property string $content
 * @property string $locked_at
 * @property string $pinned_at
 * @property string $reported_at
 * @property string $hidden_at
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property-read int $comment_count
 * @property-read int $like_count
 * @property-read Collection<Comment> $comments
 * @property-read Collection<ThreadLike> $likes
 * @property-read $user
 */
class Thread extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'threaded_id',
        'threaded_type',
        'user_id',
        'title',
        'content',
        'locked_at',
        'pinned_at',
        'reported_at',
        'hidden_at',
        'deleted_at',
    ];

    protected $appends = [
        'comment_count',
        'like_count',
    ];

    protected static function newFactory(): ThreadFactory
    {
        return ThreadFactory::new();
    }

    public function threaded(): MorphTo
    {
        return $this->morphTo('threaded');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config()->classString('threads.user_model'));
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ThreadLike::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ThreadReport::class);
    }

    public function getCommentCountAttribute(): int
    {
        return $this->comments()->count();
    }

    public function getLikeCountAttribute(): int
    {
        return $this->likes()->count();
    }
}
