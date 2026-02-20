<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PenaltyConfiguration', function (Blueprint $table) {
            $table->id('penalty_config_id');
            $table->decimal('rate_percentage', 5, 2)->default(10.00)
                ->comment('Penalty rate as percentage (e.g. 10.00 = 10%)');
            $table->boolean('is_active')->default(true);
            $table->date('effective_date');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('PenaltyConfiguration');
    }
};
