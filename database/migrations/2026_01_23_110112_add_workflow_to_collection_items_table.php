<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('collection_items', function (Blueprint $table) {

            if (!Schema::hasColumn('collection_items','item_number')) {
                $table->string('item_number')->unique()->after('id'); // J00001-001
            }

            if (!Schema::hasColumn('collection_items','qty')) {
                $table->unsignedInteger('qty')->default(1)->after('item_number');
            }

            foreach ([
                'serial_number','asset_tags','dimensions'
            ] as $col) {
                if (!Schema::hasColumn('collection_items',$col)) {
                    $table->string($col)->nullable();
                }
            }

            if (!Schema::hasColumn('collection_items','weight_kg')) {
                $table->decimal('weight_kg', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('collection_items','erasure_required')) {
                $table->boolean('erasure_required')->default(false);
            }

            // workflow
            if (!Schema::hasColumn('collection_items','status')) {
                $table->string('status')->default('created')->index();
            }

            if (!Schema::hasColumn('collection_items','collected')) {
                $table->boolean('collected')->default(false)->index();
            }

            if (!Schema::hasColumn('collection_items','collected_at')) $table->timestamp('collected_at')->nullable();
            if (!Schema::hasColumn('collection_items','processed_at')) $table->timestamp('processed_at')->nullable();

            // processing action
            if (!Schema::hasColumn('collection_items','process_action')) {
                $table->string('process_action')->nullable(); // add_to_stock / physical_destruction / recycle / resale
            }

            if (!Schema::hasColumn('collection_items','item_valuation')) {
                $table->decimal('item_valuation', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('collection_items','refurb_cost')) {
                $table->decimal('refurb_cost', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('collection_items','hdd_serial')) {
                $table->string('hdd_serial')->nullable();
            }

            if (!Schema::hasColumn('collection_items','erasure_report_path')) {
                $table->string('erasure_report_path')->nullable();
            }

            // link to created stock item (if added_to_stock)
            if (!Schema::hasColumn('collection_items','stock_item_id')) {
                $table->foreignId('stock_item_id')->nullable()
                    ->constrained('stock_items')->nullOnDelete();
            }
        });
    }

    public function down(): void {}
};
