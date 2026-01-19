<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->string('lead_code')->unique(); // e.g., L000123

            // Can convert a lead to a client later; optional link
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();

            // Prospect details (if not yet a client)
            $table->string('company_name')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            // UK address fields (optional)
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('town')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->default('UK');

            $table->string('source')->nullable(); // referral, website, call, etc.

            $table->enum('status', [
                'new', 'contacted', 'qualified', 'quoted', 'won', 'lost', 'archived'
            ])->default('new');

            $table->date('expected_collection_date')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
