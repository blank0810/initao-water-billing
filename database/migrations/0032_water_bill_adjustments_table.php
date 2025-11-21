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
        Schema::create('water_bill_adjustment', function (Blueprint $table) {
            $table->id('wba_id');
            $table->unsignedBigInteger('wb_id'); // Water Bill ID
            $table->string('adj_type'); // Type of adjustment (discount, surcharge, etc.)
            $table->decimal('amount', 10, 2);
            $table->text('reason');
            $table->unsignedBigInteger('approved_by')->nullable(); // User who approved the adjustment
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('wb_id', 'water_bill_adjustment_bill_index');
            $table->index('adj_type', 'water_bill_adjustment_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        Schema::table('water_bill_adjustment', function (Blueprint $table) {
            if (Schema::hasIndex('water_bill_adjustment', 'water_bill_adjustment_bill_index')) {
                $table->dropIndex('water_bill_adjustment_bill_index');
            }

            if (Schema::hasIndex('water_bill_adjustment', 'water_bill_adjustment_type_index')) {
                $table->dropIndex('water_bill_adjustment_type_index');
            }
        });

        Schema::dropIfExists('water_bill_adjustment');
    }
};
