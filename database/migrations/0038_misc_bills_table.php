<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('misc_bill', function (Blueprint $table) {
            $table->id('mb_id');
            $table->unsignedBigInteger('connection_id');
            $table->unsignedBigInteger('misc_reference_id');
            $table->string('bill_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->text('description');
            $table->date('billing_date');
            $table->date('due_date');
            $table->boolean('is_paid')->default(false);
            $table->date('paid_date')->nullable();
            $table->unsignedBigInteger('stat_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('connection_id', 'misc_bill_connection_index');
            $table->index('bill_number', 'misc_bill_number_index');
            $table->index('billing_date', 'misc_bill_billing_date_index');
            $table->index('is_paid', 'misc_bill_paid_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        Schema::table('misc_bill', function (Blueprint $table) {
            $indexes = [
                'misc_bill_connection_index',
                'misc_bill_number_index',
                'misc_bill_billing_date_index',
                'misc_bill_paid_index'
            ];

            foreach ($indexes as $index) {
                if (Schema::hasIndex('misc_bill', $index)) {
                    $table->dropIndex($index);
                }
            }
        });

        Schema::dropIfExists('misc_bill');
    }
};
