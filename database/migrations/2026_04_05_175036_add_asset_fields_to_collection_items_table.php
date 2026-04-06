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
        Schema::table('collection_items', function (Blueprint $table) {
            // manufacturer / model
            if (!Schema::hasColumn('collection_items', 'manufacturer_id')) {
                $table->unsignedBigInteger('manufacturer_id')->nullable()->after('category_id');
            }

            if (!Schema::hasColumn('collection_items', 'manufacturer_text')) {
                $table->string('manufacturer_text', 120)->nullable()->after('manufacturer_id');
            }

            if (!Schema::hasColumn('collection_items', 'product_model_id')) {
                $table->unsignedBigInteger('product_model_id')->nullable()->after('manufacturer_text');
            }

            if (!Schema::hasColumn('collection_items', 'model_text')) {
                $table->string('model_text', 120)->nullable()->after('product_model_id');
            }

            // asset fields
            if (!Schema::hasColumn('collection_items', 'serial_number')) {
                $table->string('serial_number')->nullable()->after('model_text');
            }

            if (!Schema::hasColumn('collection_items', 'asset_tags')) {
                $table->string('asset_tags')->nullable()->after('serial_number');
            }

            if (!Schema::hasColumn('collection_items', 'storage_serial_number')) {
                $table->string('storage_serial_number')->nullable()->after('asset_tags');
            }

            if (!Schema::hasColumn('collection_items', 'second_storage_serial_number')) {
                $table->string('second_storage_serial_number')->nullable()->after('storage_serial_number');
            }

            if (!Schema::hasColumn('collection_items', 'our_asset_number')) {
                $table->string('our_asset_number')->nullable()->after('second_storage_serial_number');
            }

            // optional foreign keys
            // add only if your manufacturers and product_models tables exist
            // and names are correct in your project
            if (!Schema::hasColumn('collection_items', 'manufacturer_id_fk_done')) {
                // no-op marker not needed, keeping simple
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collection_items', function (Blueprint $table) {
            //
        });
    }
};
