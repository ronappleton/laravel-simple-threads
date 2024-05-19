<?php

declare(strict_types=1);

namespace Appleton\Threads\Policies;

use Appleton\Threads\Models\ThreadReport;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class ThreadReportPolicy
{
    use HandlesAuthorization;

    public function resolveReport(Authenticatable $user, ThreadReport $threadReport): bool
    {
        return $this->can($user, 'threads.report.resolve');
    }

    private function can(Authenticatable $user, string $ability): bool
    {
        return method_exists($user, 'can') && $user->can($ability);
    }
}
