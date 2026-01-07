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
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name');
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the pivot table first if it exists
        if (Schema::hasTable('user_roles')) {
            Schema::dropIfExists('user_roles');
        }

        Schema::dropIfExists('roles');
    }
};
