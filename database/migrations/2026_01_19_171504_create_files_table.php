<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            // Polymorphic: can attach to collections, collection_items, sales, stock_items, etc.
            $table->morphs('attachable'); // attachable_type, attachable_id

            // Visible in documents grid
            $table->string('description')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);

            // Storage metadata
            $table->string('disk')->default('public');
            $table->string('path');               // stored path
            $table->string('original_name');      // original filename
            $table->string('mime_type')->nullable();

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
