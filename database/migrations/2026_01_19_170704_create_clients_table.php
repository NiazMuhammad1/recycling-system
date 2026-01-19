<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Visible in client list
            $table->string('name');                  // e.g., "Premium Products Ltd"
            $table->string('county')->nullable();    // e.g., "Midlothian"
            $table->string('country')->default('UK');

            // Used in collection view "Client Details & Location"
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('town')->nullable();
            $table->string('postcode')->nullable();

            // Client-level default contact details (collection copies snapshot)
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('on_site_contact_name')->nullable();
            $table->string('on_site_contact_number')->nullable();

            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['name']);
            $table->index(['county']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
