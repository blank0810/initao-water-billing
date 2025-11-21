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
        Schema::create('account_type', function (Blueprint $table) {
            $table->id('at_id');
            $table->string('at_desc');
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraint (will be added in a separate migration after all tables are created)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_type');
    }
};
