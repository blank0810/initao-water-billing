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
        Schema::create('misc_reference', function (Blueprint $table) {
            $table->id('misc_reference_id');
            $table->string('reference_type'); // e.g., 'penalty', 'surcharge', 'discount', 'other'
            $table->string('reference_code')->unique();
            $table->string('description');
            $table->decimal('default_amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('stat_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('reference_type', 'misc_reference_type_index');
            $table->index('reference_code', 'misc_reference_code_index');
            $table->index('is_active', 'misc_reference_active_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        Schema::table('misc_reference', function (Blueprint $table) {
            $indexes = [
                'misc_reference_type_index',
                'misc_reference_code_index',
                'misc_reference_active_index',
            ];

            foreach ($indexes as $index) {
                if (Schema::hasIndex('misc_reference', $index)) {
                    $table->dropIndex($index);
                }
            }
        });

        Schema::dropIfExists('misc_reference');
    }
};
