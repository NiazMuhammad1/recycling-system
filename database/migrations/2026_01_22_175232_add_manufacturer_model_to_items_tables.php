<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // collection_items
        Schema::table('collection_items', function (Blueprint $table) {
            $table->foreignId('manufacturer_id')->nullable()->after('category_id')
                  ->constrained('manufacturers')->nullOnDelete();

            $table->foreignId('product_model_id')->nullable()->after('manufacturer_id')
                  ->constrained('product_models')->nullOnDelete();

            // optional fallback (if you want free typing too)
            $table->string('manufacturer_text')->nullable()->after('product_model_id');
            $table->string('model_text')->nullable()->after('manufacturer_text');
        });

        // stock_items
        Schema::table('stock_items', function (Blueprint $table) {
            $table->foreignId('manufacturer_id')->nullable()->after('category_id')
                  ->constrained('manufacturers')->nullOnDelete();

            $table->foreignId('product_model_id')->nullable()->after('manufacturer_id')
                  ->constrained('product_models')->nullOnDelete();

            $table->string('manufacturer_text')->nullable()->after('product_model_id');
            $table->string('model_text')->nullable()->after('manufacturer_text');
        });
    }

    public function down(): void
    {
        Schema::table('collection_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manufacturer_id');
            $table->dropConstrainedForeignId('product_model_id');
            $table->dropColumn(['manufacturer_text','model_text']);
        });

        Schema::table('stock_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manufacturer_id');
            $table->dropConstrainedForeignId('product_model_id');
            $table->dropColumn(['manufacturer_text','model_text']);
        });
    }
};
