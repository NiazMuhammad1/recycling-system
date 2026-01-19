<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('collection_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();

            // Visible: item number like J16003-01 / J24004-01
            $table->string('item_code')->unique();

            // Visible in items list
            $table->unsignedInteger('quantity')->default(1);

            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->string('manufacturer')->nullable(); // e.g., Dell / Apple
            $table->string('model')->nullable();        // e.g., Latitude / Mac

            $table->string('serial_number')->nullable();
            $table->string('asset_tags')->nullable();   // shown as "Asset Tag(s)"

            // Visible checkbox "Erasure Req."
            $table->boolean('erasure_required')->default(false);

            // Visible: "Collected" checkbox per row in edit screen
            $table->boolean('is_collected')->default(false);

            // Visible: status in item status report / grid
            $table->enum('status', [
                'created',
                'pending',
                'collected',
                'processing',
                'processed',
                'add_to_stock',
                'physical_destruction',
                'reused',
                'recycled'
            ])->default('created');

            // Visible: "Erasure Report" column (link text like "Physical Destruction")
            $table->string('erasure_report_label')->nullable(); // e.g., "Physical Destruction"
            $table->dateTime('erasure_completed_at')->nullable();

            // When moved into stock module
            $table->foreignId('stock_item_id')->nullable()->constrained('stock_items')->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['collection_id']);
            $table->index(['status']);
            $table->index(['serial_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_items');
    }
};
