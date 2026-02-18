<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('DocumentSignatory', function (Blueprint $table) {
            $table->id();
            $table->string('position_key', 50)->unique();
            $table->string('position_title', 100);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('default_role', 50)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DocumentSignatory');
    }
};
