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
            // client
            $table->longText('client_signature')->nullable();
            $table->string('client_print_name')->nullable();

            $table->longText('driver_signature')->nullable();
            $table->string('driver_print_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn([
                'client_signature','client_print_name',
                'driver_user_id','driver_signature','driver_print_name',
            ]);
        });
    }
};
