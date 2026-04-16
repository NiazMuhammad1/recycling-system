<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_items', 'asset_tags')) {
                $table->string('asset_tags', 255)->nullable()->after('serial_number');
            }

            if (!Schema::hasColumn('stock_items', 'condition_notes')) {
                $table->text('condition_notes')->nullable()->after('cosmetic_condition');
            }

            if (!Schema::hasColumn('stock_items', 'year')) {
                $table->string('year', 50)->nullable()->after('model');
            }

            if (!Schema::hasColumn('stock_items', 'processor_manufacturer')) {
                $table->string('processor_manufacturer', 120)->nullable()->after('year');
            }

            if (!Schema::hasColumn('stock_items', 'processor_type')) {
                $table->string('processor_type', 120)->nullable()->after('processor_manufacturer');
            }

            if (!Schema::hasColumn('stock_items', 'processor_speed_ghz')) {
                $table->decimal('processor_speed_ghz', 8, 2)->nullable()->after('processor_type');
            }

            if (!Schema::hasColumn('stock_items', 'ram_gb')) {
                $table->decimal('ram_gb', 8, 2)->nullable()->after('ram_type');
            }

            if (!Schema::hasColumn('stock_items', 'hdd_gb')) {
                $table->decimal('hdd_gb', 10, 2)->nullable()->after('ram_gb');
            }

            if (!Schema::hasColumn('stock_items', 'ssd_gb')) {
                $table->decimal('ssd_gb', 10, 2)->nullable()->after('hdd_gb');
            }

            if (!Schema::hasColumn('stock_items', 'nvme_gb')) {
                $table->decimal('nvme_gb', 10, 2)->nullable()->after('ssd_gb');
            }

            if (!Schema::hasColumn('stock_items', 'operating_system')) {
                $table->string('operating_system', 255)->nullable()->after('nvme_gb');
            }

            if (!Schema::hasColumn('stock_items', 'charger_included')) {
                $table->boolean('charger_included')->default(false)->after('operating_system');
            }

            if (!Schema::hasColumn('stock_items', 'accessories_included')) {
                $table->boolean('accessories_included')->default(false)->after('charger_included');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            //
        });
    }
};
