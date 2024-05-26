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
use Illuminate\Database\Eloquent\Relations\Relation;
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
 * @property-read Relation $user
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

    /**
     * @phpstan-ignore-next-line
     */
    public function threaded(): MorphTo
    {
        return $this->morphTo('threaded');
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function user(): BelongsTo
    {
        /** @phpstan-ignore-next-line */
        return $this->belongsTo(config()->classString('threads.user_model'));
    }

    /**
     * @return HasMany<Comment>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return HasMany<ThreadLike>
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ThreadLike::class);
    }

    /**
     * @return HasMany<ThreadReport>
     */
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

    /**
     * @param  array<int, string>  $relations
     */
    public function deepNestedRelation(array $relations, ?Model $relation = null): ?Model
    {
        $relation ??= $this;

        if (empty($relations)) {
            return $relation;
        }

        $relationName = array_shift($relations);

        if (! method_exists($relation, $relationName)) {
            return null;
        }

        return $this->deepNestedRelation($relations, $relation->$relationName);
    }

    /**
     * @param  array<int, array<int, string>>  $relations
     * @return Collection<int, Model>
     */
    public function deepNestedRelations(array $relations): Collection
    {
        $foundRelations = collect();

        collect($relations)->each(function ($relation) use ($foundRelations) {
            $foundRelations->push($this->deepNestedRelation($relation));
        });

        return $foundRelations;
    }
}
