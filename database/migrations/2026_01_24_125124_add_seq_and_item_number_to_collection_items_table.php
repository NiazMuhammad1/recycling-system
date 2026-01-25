<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('collection_items', function (Blueprint $table) {
            // sequence within collection (1,2,3...) used for J00001-001
            $table->unsignedInteger('seq')->nullable()->after('collection_id');

            // human readable item number
            $table->string('item_number', 30)->nullable()->after('seq');

            // ensure uniqueness inside a collection
            $table->unique(['collection_id', 'seq'], 'collection_items_collection_seq_unique');
            $table->unique(['collection_id', 'item_number'], 'collection_items_collection_item_number_unique');

            $table->index(['collection_id', 'seq']);
        });
    }

    public function down(): void
    {
        Schema::table('collection_items', function (Blueprint $table) {
            $table->dropUnique('collection_items_collection_seq_unique');
            $table->dropUnique('collection_items_collection_item_number_unique');
            $table->dropIndex(['collection_id', 'seq']);
            $table->dropColumn(['seq', 'item_number']);
        });
    }
};
