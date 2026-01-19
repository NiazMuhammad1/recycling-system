<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->morphs('subject'); // subject_type, subject_id (collection, item, stock, sale)

            $table->string('event');   // created, updated, collected, processed, sent_to_client, etc.
            $table->text('message')->nullable();

            $table->json('meta')->nullable(); // before/after snapshots or extra info

            $table->timestamps();

            $table->index(['event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
