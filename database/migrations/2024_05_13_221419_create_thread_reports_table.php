<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thread_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('thread_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('comment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->text('reason');
            $table->timestamp('resolved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thread_reports');
    }
};
