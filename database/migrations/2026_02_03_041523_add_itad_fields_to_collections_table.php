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
        Schema::table('collections', function (Blueprint $table) {
            // Partner (assumes you have a partners table; if not, change to string)
            $table->unsignedBigInteger('partner_id')->nullable()->after('client_id');

            // SLA
            $table->unsignedInteger('sla_target')->nullable()->after('collection_date');

            // Transport provider
            $table->string('transport_provider_name')->nullable();
            $table->string('transport_provider_registration_no')->nullable();
            $table->text('transport_provider_address')->nullable();

            // ITAD fields
            $table->string('adisa_dial_rating')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            //
        });
    }
};
