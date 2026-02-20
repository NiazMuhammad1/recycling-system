<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('collection_item_hdds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('collection_item_id')->constrained('collection_items')->cascadeOnDelete();

            // optional catalog links (use your existing Manufacturer + ProductModel)
            $table->foreignId('manufacturer_id')->nullable()->constrained('manufacturers');
            $table->foreignId('product_model_id')->nullable()->constrained('product_models');

            // tag-text support like your items grid
            $table->string('manufacturer_text', 120)->nullable();
            $table->string('model_text', 120)->nullable();

            $table->string('serial', 255)->nullable();
            $table->string('status', 30)->default('not_processed'); // not_processed / erased / failed etc.
            $table->string('erasure_report_path')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_item_hdds');
    }
};
