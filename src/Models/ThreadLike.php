<?php

declare(strict_types=1);

namespace Appleton\Threads\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $thread_id
 * @property string $user_id
 * @property-read Thread $thread
 * @property-read Relation $user
 */
class ThreadLike extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'thread_id',
        'user_id',
    ];

    /**
     * @return BelongsTo<Thread, ThreadLike>
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function user(): BelongsTo
    {
        /** @phpstan-ignore-next-line */
        return $this->belongsTo(config()->classString('threads.user_model'));
    }
}
