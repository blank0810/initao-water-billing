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
        Schema::create('customer', function (Blueprint $table) {
            $table->id('cust_id');
            $table->dateTime('create_date');
            $table->string('cust_last_name');
            $table->string('cust_first_name');
            $table->string('cust_middle_name')->nullable();
            $table->unsignedBigInteger('ca_id');
            $table->string('land_mark')->nullable();
            $table->unsignedBigInteger('stat_id');
            $table->string('c_type');
            $table->string('resolution_no')->nullable();
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index(['cust_last_name', 'cust_first_name'], 'customer_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop index if exists
        Schema::table('customer', function (Blueprint $table) {
            if (Schema::hasIndex('customer', 'customer_name_index')) {
                $table->dropIndex('customer_name_index');
            }
        });

        Schema::dropIfExists('customer');
    }
};
