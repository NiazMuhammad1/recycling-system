<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->foreign('source_collection_id')
                ->references('id')->on('collections')
                ->nullOnDelete();

            $table->foreign('source_collection_item_id')
                ->references('id')->on('collection_items')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->dropForeign(['source_collection_id']);
            $table->dropForeign(['source_collection_item_id']);
        });
    }
};
