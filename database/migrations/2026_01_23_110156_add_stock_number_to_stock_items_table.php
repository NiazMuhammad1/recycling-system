<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('stock_items', function (Blueprint $table) {
      if (!Schema::hasColumn('stock_items','stock_number')) {
        $table->string('stock_number')->unique()->after('id'); // S1600003
      }
      if (!Schema::hasColumn('stock_items','status')) {
        $table->string('status')->default('in_stock')->index();
      }
    });
  }
  public function down(): void {}
};
