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
        Schema::create('town', function (Blueprint $table) {
            $table->id('t_id');
            $table->string('t_desc');
            $table->unsignedBigInteger('prov_id');
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('prov_id')
                  ->references('prov_id')
                  ->on('province')
                  ->onDelete('restrict');
                  
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
            
            // Add index for search optimization
            $table->index('t_desc', 'town_desc_index');
        });
        
        // Insert some default towns (example for one province)
        $towns = [
            ['t_desc' => 'Bangued', 'prov_id' => 1, 'stat_id' => 2], // Assuming Abra is prov_id 1
            ['t_desc' => 'Boliney', 'prov_id' => 1, 'stat_id' => 2],
            ['t_desc' => 'Bucay', 'prov_id' => 1, 'stat_id' => 2],
            // Add more towns as needed
        ];
        
        // Add timestamps to each town
        $now = now();
        $towns = array_map(function($town) use ($now) {
            return array_merge($town, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $towns);
        
        DB::table('town')->insert($towns);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first if they exist
        Schema::table('town', function (Blueprint $table) {
            $foreignKeys = ['prov_id', 'stat_id'];
            
            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('town', $column)) {
                    $table->dropForeign(["town_{$column}_foreign"]);
                }
            }
            
            // Drop index if exists
            if (Schema::hasIndex('town', 'town_desc_index')) {
                $table->dropIndex('town_desc_index');
            }
        });
        
        Schema::dropIfExists('town');
    }
};
