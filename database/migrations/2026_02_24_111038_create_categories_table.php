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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->string('name');                 // Description -> name
            $table->string('ewc_code')->nullable(); // e.g. 20:01:35

            // Optional defaults for paperwork
            $table->decimal('default_weight_kg', 10, 2)->nullable(); // approx weight per item / typical weight
            $table->string('component')->nullable();                // e.g. Lead, Mercury
            $table->string('concentration')->nullable();            // e.g. "Up to 2%" / "Approx 0.5kg"
            $table->string('physical_form')->nullable();            // e.g. Solid
            $table->string('hazard_codes')->nullable();             // e.g. H6 or "H6,H14"

            // Category type: both | hazard | duty_of_care
            $table->enum('type', ['both', 'hazard', 'duty_of_care'])->default('both');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['name', 'type']); // prevents duplicates per type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
