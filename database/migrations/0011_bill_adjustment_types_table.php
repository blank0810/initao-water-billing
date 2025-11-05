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
        Schema::create('BillAdjustmentType', function (Blueprint $table) {
            $table->id('bill_adjustment_type_id');
            $table->string('name');
            $table->enum('direction', ['debit', 'credit']);
            $table->unsignedBigInteger('stat_id');

            // Foreign key constraint (will be added in a separate migration after all tables are created)
        });

        // Insert default adjustment types
        DB::table('BillAdjustmentType')->insert([
            ['name' => 'Meter Reading Error', 'direction' => 'credit', 'stat_id' => DB::table('statuses')->where('stat_desc', 'ACTIVE')->value('stat_id')],
            ['name' => 'Billing Error', 'direction' => 'credit', 'stat_id' => DB::table('statuses')->where('stat_desc', 'ACTIVE')->value('stat_id')],
            ['name' => 'Penalty Waiver', 'direction' => 'credit', 'stat_id' => DB::table('statuses')->where('stat_desc', 'ACTIVE')->value('stat_id')],
            ['name' => 'Surcharge', 'direction' => 'debit', 'stat_id' => DB::table('statuses')->where('stat_desc', 'ACTIVE')->value('stat_id')],
            ['name' => 'Other', 'direction' => 'debit', 'stat_id' => DB::table('statuses')->where('stat_desc', 'ACTIVE')->value('stat_id')],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('BillAdjustmentType');
    }
};
