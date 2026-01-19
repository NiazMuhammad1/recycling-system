<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();

            // Visible: "Collection J16003"
            $table->string('collection_code')->unique();

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            // Visible: "Collection Date"
            $table->dateTime('collection_date')->nullable();

            // Visible: Created/Collected/Processing/Processed (+ Pending on dashboard)
            $table->enum('status', [
                'created', 'client_confirmed', 'pending', 'collected', 'processing', 'processed', 'cancelled'
            ])->default('created');

            // Snapshot of client location shown in collection view
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('town')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->default('UK');

            // Visible: "Contact Details"
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('on_site_contact_name')->nullable();
            $table->string('on_site_contact_number')->nullable();

            // Visible: "Collection Details"
            $table->string('data_sanitisation')->nullable(); // e.g., "No Sanitation Required"
            $table->string('collection_type')->nullable();   // e.g., "IT Asset Remarketing (Resale)"
            $table->text('logistics')->nullable();

            // Visible questions block
            $table->text('equipment_location')->nullable();     // "Where is the equipment located..."
            $table->text('access_elevator')->nullable();        // "Access to suitable elevator?"
            $table->text('route_restrictions')->nullable();     // "Restrictions on route..."
            $table->text('other_information')->nullable();      // "Any other relevant information?"

            // Visible: "Internal Use" -> Notes
            $table->text('internal_notes')->nullable();

            // Visible blocks (exist even if currently empty on screen)
            $table->text('pre_collection_audit')->nullable();
            $table->text('equipment_classification')->nullable();

            // Visible header amounts: Value / Sold / Costs / Profit
            $table->decimal('value_amount', 12, 2)->default(0);
            $table->decimal('sold_amount', 12, 2)->default(0);
            $table->decimal('costs_amount', 12, 2)->default(0);
            $table->decimal('profit_amount', 12, 2)->default(0);

            // Audit trail timestamps (Created, Client Confirmed, Collected, Processed)
            $table->dateTime('client_confirmed_at')->nullable();
            $table->dateTime('collected_at')->nullable();
            $table->dateTime('processing_started_at')->nullable();
            $table->dateTime('processed_at')->nullable();

            // Visible: "Send to client for confirmation"
            $table->dateTime('sent_to_client_at')->nullable();
            $table->foreignId('sent_to_client_by')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['status']);
            $table->index(['collection_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
