<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->string('sale_number')->unique(); // X1600001
            $table->dateTime('sale_date')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Visible: Customer details
            $table->string('customer_name');
            $table->string('street')->nullable();
            $table->string('town')->nullable();
            $table->string('county')->nullable();
            $table->string('country')->default('UK');
            $table->string('postcode')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();

            // Strongly recommended for UK invoicing (even if not visible yet)
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(0);     // 20.00 etc
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['sale_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
