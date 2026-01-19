<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();

            // Visible: "S1600003"
            $table->string('stock_number')->unique();

            // Visible column SKU (may be blank)
            $table->string('sku')->nullable();

            // Visible: Serial
            $table->string('serial_number')->nullable()->index();

            // Visible: "Item" column (e.g., "Dell - Optiplex")
            $table->string('item_name')->nullable();

            // Visible in model details
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('manufacturer')->nullable(); // Dell
            $table->string('model')->nullable();        // Optiplex

            // Visible columns
            $table->string('chassis')->nullable();      // Desktop / Cube / Micro Tower
            $table->string('processor')->nullable();    // Intel
            $table->string('memory')->nullable();       // 2.00 Gb
            $table->string('hdd')->nullable();          // 500 Gb

            // Visible on view page
            $table->decimal('price', 12, 2)->default(0);
            $table->string('cosmetic_condition')->nullable(); // "A"
            $table->string('condition')->nullable();          // separate from cosmetic
            $table->string('warehouse_location')->nullable(); // "B2"

            // Visible: Fully Functional (Yes/No)
            $table->boolean('fully_functional')->default(false);

            // Extra model details visible on view page
            $table->string('type')->nullable();         // Pentium IV (as shown)
            $table->string('speed')->nullable();        // 600.00 Ghz
            $table->string('ram')->nullable();          // 2.00 Gb (if separate from memory)
            $table->string('ram_type')->nullable();     // DDR3
            $table->string('os')->nullable();           // Windows XP
            $table->string('optical_drives')->nullable();

            $table->text('notes')->nullable();

            // Link back to origin (important for traceability)
            $table->unsignedBigInteger('source_collection_id')->nullable()->index();
            $table->unsignedBigInteger('source_collection_item_id')->nullable()->index();

            $table->enum('status', [
                'in_stock', 'reserved', 'sold', 'written_off'
            ])->default('in_stock');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['manufacturer', 'model']);
            $table->index(['warehouse_location']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
