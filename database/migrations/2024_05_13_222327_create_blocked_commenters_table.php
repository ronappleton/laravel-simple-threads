<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_commenters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('blocked_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('blocker_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason');
            $table->boolean('is_permanent')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->text('unblock_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['blocked_user_id', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_commenters');
    }
};
