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
        Schema::create('collection_item_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_item_id')->constrained()->cascadeOnDelete();
            $table->string('item_prefix'); // e.g. OLE954
            $table->integer('seq');        // 1,2,3...
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_item_codes');
    }
};
