<?php

declare(strict_types=1);

namespace Appleton\Threads\Models;

use Database\Factories\ThreadReportFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read string $id
 * @property string $user_id
 * @property string $thread_id
 * @property string $comment_id
 * @property string $reason
 * @property string $resolved_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property-read $user
 * @property-read Thread $thread
 * @property-read Comment $comment
 */
class ThreadReport extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'thread_id',
        'comment_id',
        'reason',
        'resolved_at',
        'deleted_at',
        'reported_at',
    ];

    /**
     * @return array<string, mixed>
     */
    public function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    protected static function newFactory(): ThreadReportFactory
    {
        return ThreadReportFactory::new();
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config()->classString('threads.user_model'));
    }
}
