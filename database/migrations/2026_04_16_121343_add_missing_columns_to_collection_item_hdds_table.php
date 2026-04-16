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
        Schema::table('collection_item_hdds', function (Blueprint $table) {
            if (!Schema::hasColumn('collection_item_hdds', 'size')) {
                $table->string('size', 50)->nullable()->after('status');
            }

            if (!Schema::hasColumn('collection_item_hdds', 'create_separate_stock_item')) {
                $table->boolean('create_separate_stock_item')->default(false)->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collection_item_hdds', function (Blueprint $table) {
            //
        });
    }
};
