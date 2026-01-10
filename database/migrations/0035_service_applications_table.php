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
        Schema::create('ServiceApplication', function (Blueprint $table) {
            $table->id('application_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('address_id');
            $table->string('application_number')->unique();
            $table->dateTime('submitted_at');
            $table->dateTime('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('application_number', 'service_application_number_index');
            $table->index('submitted_at', 'service_application_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        Schema::table('ServiceApplication', function (Blueprint $table) {
            if (Schema::hasIndex('ServiceApplication', 'service_application_number_index')) {
                $table->dropIndex('service_application_number_index');
            }

            if (Schema::hasIndex('ServiceApplication', 'service_application_date_index')) {
                $table->dropIndex('service_application_date_index');
            }
        });

        Schema::dropIfExists('ServiceApplication');
    }
};
