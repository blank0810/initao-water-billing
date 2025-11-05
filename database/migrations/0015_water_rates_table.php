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
        Schema::create('water_rates', function (Blueprint $table) {
            $table->id('wr_id');
            $table->string('rate_desc');
            $table->decimal('rate', 10, 5);
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
            
            // Add index for search optimization
            $table->index('rate_desc', 'water_rate_desc_index');
        });
        
        // Insert default water rates
        DB::table('water_rates')->insert([
            [
                'rate_desc' => 'Residential - First 10 cubic meters',
                'rate' => 100.00,
                'stat_id' => 2, // Assuming 2 is ACTIVE status
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rate_desc' => 'Residential - 11-20 cubic meters',
                'rate' => 15.00,
                'stat_id' => 2, // Assuming 2 is ACTIVE status
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rate_desc' => 'Commercial - First 10 cubic meters',
                'rate' => 150.00,
                'stat_id' => 2, // Assuming 2 is ACTIVE status
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rate_desc' => 'Commercial - 11-20 cubic meters',
                'rate' => 20.00,
                'stat_id' => 2, // Assuming 2 is ACTIVE status
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key first if it exists
        Schema::table('water_rates', function (Blueprint $table) {
            if (Schema::hasColumn('water_rates', 'stat_id')) {
                $table->dropForeign(['water_rates_stat_id_foreign']);
            }
            
            // Drop index if exists
            if (Schema::hasIndex('water_rates', 'water_rate_desc_index')) {
                $table->dropIndex('water_rate_desc_index');
            }
        });
        
        Schema::dropIfExists('water_rates');
    }
};
