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
        Schema::create('collection_pdf_emails', function (Blueprint $table) {

            $table->id();

            $table->foreignId('collection_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('email');

            // selected pdfs
            $table->json('pdfs')->nullable();

            $table->enum('status', [
                'pending',
                'sent',
                'failed'
            ])->default('pending');

            $table->timestamp('sent_at')->nullable();

            $table->text('error')->nullable();

            $table->foreignId('sent_by')
                ->nullable()
                ->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_pdf_emails');
    }
};