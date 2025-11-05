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
            
            // Foreign key constraints
            $table->foreign('c_id')
                  ->references('c_id')
                  ->on('consumer')
                  ->onDelete('restrict');
                  
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');
            
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
        // Drop foreign keys first if they exist
        Schema::table('consumer_ledger', function (Blueprint $table) {
            $foreignKeys = ['c_id', 'stat_id', 'user_id'];
            
            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('consumer_ledger', $column)) {
                    $table->dropForeign(["consumer_ledger_{$column}_foreign"]);
                }
            }
            
            // Drop indexes if they exist
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
