<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->string('vehicles_used')->nullable()->after('equipment_classification');
            $table->string('staff_members')->nullable()->after('vehicles_used');
        });
    }

    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['vehicles_used', 'staff_members']);
        });
    }
};
