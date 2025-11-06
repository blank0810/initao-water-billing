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
        Schema::create('period', function (Blueprint $table) {
            $table->id('per_id');
            $table->string('per_name'); // e.g., 'January 2025', 'February 2025'
            $table->string('per_code')->unique(); // e.g., '202501', '202502'
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_closed')->default(false);
            $table->dateTime('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();
            
            // Add unique constraint for date range
            $table->unique(['start_date', 'end_date'], 'period_date_range_unique');
            
            // Foreign key constraints
            $table->foreign('closed_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
            
            // Add index for search optimization
            $table->index('per_code', 'period_code_index');
            $table->index('start_date', 'period_start_date_index');
            $table->index('is_closed', 'period_closed_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first if they exist
        Schema::table('period', function (Blueprint $table) {
            $foreignKeys = ['closed_by', 'stat_id'];
            
            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('period', $column)) {
                    $table->dropForeign(["period_{$column}_foreign"]);
                }
            }
            
            // Drop indexes if they exist
            $indexes = [
                'period_code_index',
                'period_start_date_index',
                'period_closed_index',
                'period_date_range_unique'
            ];
            
            foreach ($indexes as $index) {
                if (Schema::hasIndex('period', $index)) {
                    $table->dropIndex($index);
                }
            }
        });
        
        Schema::dropIfExists('period');
    }
};
