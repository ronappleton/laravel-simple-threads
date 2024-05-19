<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->morphs('threaded');
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('pinned_at')->nullable();
            $table->timestamp('hidden_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
