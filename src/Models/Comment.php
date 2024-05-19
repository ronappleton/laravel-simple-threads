<?php

declare(strict_types=1);

namespace Appleton\Threads\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read string $id
 * @property string $thread_id
 * @property string $user_id
 * @property string $content
 * @property string $reported_at
 * @property string $hidden_at
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property-read Thread $thread
 * @property-read $user
 */
class Comment extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'thread_id',
        'user_id',
        'content',
        'reported_at',
        'hidden_at',
        'deleted_at',
    ];

    protected static function newFactory(): CommentFactory
    {
        return CommentFactory::new();
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config()->classString('threads.user_model'));
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ThreadReport::class);
    }
}
