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

            $table->string('category_name')->nullable()->after('category_id');
            $table->string('ewc_code')->nullable();
            $table->string('component')->nullable();
            $table->string('concentration')->nullable();
            $table->string('physical_form')->nullable();
            $table->string('hazard_codes')->nullable();

            $table->decimal('weight_kg',10,3)->default(0)->change();
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
