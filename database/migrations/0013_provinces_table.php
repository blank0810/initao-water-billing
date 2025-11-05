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
        Schema::create('province', function (Blueprint $table) {
            $table->id('prov_id');
            $table->string('prov_desc');
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
            
            // Add index for search optimization
            $table->index('prov_desc', 'province_desc_index');
        });
        
        // Insert some default provinces (Philippines as example)
        $provinces = [
            ['prov_desc' => 'Abra', 'stat_id' => 2], // ACTIVE
            ['prov_desc' => 'Agusan del Norte', 'stat_id' => 2],
            ['prov_desc' => 'Agusan del Sur', 'stat_id' => 2],
            ['prov_desc' => 'Aklan', 'stat_id' => 2],
            ['prov_desc' => 'Albay', 'stat_id' => 2],
            // Add more provinces as needed
        ];
        
        // Add timestamps to each province
        $now = now();
        $provinces = array_map(function($province) use ($now) {
            return array_merge($province, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $provinces);
        
        DB::table('province')->insert($provinces);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key first if it exists
        Schema::table('province', function (Blueprint $table) {
            if (Schema::hasColumn('province', 'stat_id')) {
                $table->dropForeign(['province_stat_id_foreign']);
            }
            
            // Drop index if exists
            if (Schema::hasIndex('province', 'province_desc_index')) {
                $table->dropIndex('province_desc_index');
            }
        });
        
        Schema::dropIfExists('province');
    }
};
