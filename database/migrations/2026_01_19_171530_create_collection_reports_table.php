<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('collection_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();

            $table->enum('type', [
                'data_destruction_certificate',
                'item_status_report',
                'export_csv',
                'manifest'
            ]);

            // Generated file (PDF/CSV)
            $table->foreignId('file_id')->nullable()->constrained('files')->nullOnDelete();

            $table->dateTime('generated_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['collection_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_reports');
    }
};
