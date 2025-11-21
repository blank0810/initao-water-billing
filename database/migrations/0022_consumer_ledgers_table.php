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
        Schema::create('consumer_ledger', function (Blueprint $table) {
            $table->id('cl_id');
            $table->unsignedBigInteger('c_id'); // Consumer ID
            $table->string('cl_no'); // Ledger number/reference
            $table->decimal('debit', 10, 2)->default(0);
            $table->decimal('credit', 10, 2)->default(0);
            $table->decimal('balance', 10, 2);
            $table->dateTime('create_date');
            $table->string('or_no')->nullable(); // Official receipt number
            $table->unsignedBigInteger('stat_id');
            $table->unsignedBigInteger('user_id'); // User who created the entry
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('c_id', 'consumer_ledger_consumer_index');
            $table->index('cl_no', 'consumer_ledger_number_index');
            $table->index('create_date', 'consumer_ledger_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        Schema::table('consumer_ledger', function (Blueprint $table) {
            $indexes = [
                'consumer_ledger_consumer_index',
                'consumer_ledger_number_index',
                'consumer_ledger_date_index'
            ];

            foreach ($indexes as $index) {
                if (Schema::hasIndex('consumer_ledger', $index)) {
                    $table->dropIndex($index);
                }
            }
        });

        Schema::dropIfExists('consumer_ledger');
    }
};
