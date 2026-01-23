<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            if (!Schema::hasColumn('collections','collection_number')) {
                $table->string('collection_number')->unique()->after('id'); // J00001
            }

            if (!Schema::hasColumn('collections','status')) {
                $table->string('status')->default('created')->index(); // created/collected/processed
            }

            if (!Schema::hasColumn('collections','collection_date')) {
                $table->dateTime('collection_date')->nullable();
            }

            // Client snapshot fields (so changing client later doesn't change historic collection)
            foreach ([
                'address_line_1','address_line_2','town','county','postcode','country',
                'contact_name','contact_email','contact_number',
                'on_site_contact_name','on_site_contact_number'
            ] as $col) {
                if (!Schema::hasColumn('collections',$col)) {
                    $table->string($col)->nullable();
                }
            }

            if (!Schema::hasColumn('collections','vehicles_used')) $table->string('vehicles_used')->nullable();
            if (!Schema::hasColumn('collections','staff_members')) $table->string('staff_members')->nullable();

            // Collection details text areas
            foreach ([
                'equipment_location','access_elevator','route_restrictions','other_information',
                'internal_notes','pre_collection_audit','equipment_classification'
            ] as $col) {
                if (!Schema::hasColumn('collections',$col)) {
                    $table->text($col)->nullable();
                }
            }

            if (!Schema::hasColumn('collections','data_sanitisation')) $table->string('data_sanitisation')->nullable();
            if (!Schema::hasColumn('collections','collection_type'))   $table->string('collection_type')->nullable();
            if (!Schema::hasColumn('collections','logistics'))         $table->string('logistics')->nullable();

            if (!Schema::hasColumn('collections','collected_at')) $table->timestamp('collected_at')->nullable();
            if (!Schema::hasColumn('collections','processed_at')) $table->timestamp('processed_at')->nullable();
        });
    }

    public function down(): void
    {
        // keep it simple for now (optional)
    }
};
