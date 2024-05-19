<?php

declare(strict_types=1);

namespace Appleton\Threads\Models;

use Database\Factories\BlockedCommenterFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $blocked_user_id
 * @property string $blocker_user_id
 * @property string $reason
 * @property string $is_permanent
 * @property string $expires_at
 * @property string $unblock_reason
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property-read $blockedUser
 * @property-read $blockerUser
 */
class BlockedCommenter extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'blocked_user_id',
        'blocker_user_id',
        'reason',
        'unblock_reason',
        'is_permanent',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected static function newFactory(): BlockedCommenterFactory
    {
        return BlockedCommenterFactory::new();
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function blockedUser(): BelongsTo
    {
        return $this->belongsTo(config()->classString('threads.user_model'), 'blocked_user_id');
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function blockerUser(): BelongsTo
    {
        return $this->belongsTo(config()->classString('threads.user_model'), 'blocker_user_id');
    }
}
