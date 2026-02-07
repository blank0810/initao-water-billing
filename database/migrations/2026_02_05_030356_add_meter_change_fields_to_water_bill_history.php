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
        Schema::table('water_bill_history', function (Blueprint $table) {
            $table->boolean('is_meter_change')->default(false)->after('adjustment_total');
            $table->unsignedBigInteger('old_assignment_id')->nullable()->after('is_meter_change');
            $table->decimal('old_meter_consumption', 12, 3)->nullable()->after('old_assignment_id');
            $table->decimal('new_meter_consumption', 12, 3)->nullable()->after('old_meter_consumption');

            $table->foreign('old_assignment_id')
                  ->references('assignment_id')
                  ->on('MeterAssignment')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_bill_history', function (Blueprint $table) {
            $table->dropForeign(['old_assignment_id']);
            $table->dropColumn(['is_meter_change', 'old_assignment_id', 'old_meter_consumption', 'new_meter_consumption']);
        });
    }
};
