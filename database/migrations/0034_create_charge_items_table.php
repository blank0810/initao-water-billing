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
        Schema::create('ChargeItem', function (Blueprint $table) {
            $table->id('charge_item_id');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('default_amount', 10, 2);
            $table->enum('charge_type', ['one_time', 'recurring', 'usage_based']);
            $table->boolean('is_taxable')->default(false);
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraint (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('code', 'charge_item_code_index');
            $table->index('charge_type', 'charge_item_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ChargeItem', function (Blueprint $table) {
            // Drop indexes if they exist
            if (Schema::hasIndex('ChargeItem', 'charge_item_code_index')) {
                $table->dropIndex('charge_item_code_index');
            }

            if (Schema::hasIndex('ChargeItem', 'charge_item_type_index')) {
                $table->dropIndex('charge_item_type_index');
            }
        });

        Schema::dropIfExists('ChargeItem');
    }
};
