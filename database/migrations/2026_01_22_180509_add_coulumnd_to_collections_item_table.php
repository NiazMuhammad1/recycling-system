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
            $table->string('status')->default('created')->index();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('added_to_stock_at')->nullable();

            $table->string('action')->nullable(); // add_to_stock, physical_destruction, resale, etc.
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
