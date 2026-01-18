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
        Schema::create('uploaded_readings', function (Blueprint $table) {
            $table->id('uploaded_reading_id');

            // Reference to reading schedule
            $table->unsignedBigInteger('schedule_id');

            // Data inherited from consumer info API
            $table->unsignedBigInteger('connection_id');
            $table->string('account_no', 50)->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('area_desc', 100)->nullable();
            $table->string('account_type_desc', 100)->nullable();
            $table->string('connection_status', 50)->nullable();
            $table->string('meter_serial', 50)->nullable();
            $table->decimal('previous_reading', 10, 3)->nullable();
            $table->decimal('arrear', 12, 2)->default(0);
            $table->decimal('penalty', 12, 2)->default(0);
            $table->integer('sequence_order')->default(0);
            $table->string('entry_status', 50)->nullable();

            // New data from device
            $table->decimal('present_reading', 10, 3)->nullable();
            $table->date('reading_date')->nullable();
            $table->decimal('site_bill_amount', 12, 2)->nullable();
            $table->boolean('is_printed')->default(false);
            $table->boolean('is_scanned')->default(false);

            // User who uploaded the reading
            $table->unsignedBigInteger('user_id');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            // Foreign keys
            $table->foreign('schedule_id')->references('schedule_id')->on('reading_schedule')->onDelete('cascade');
            $table->foreign('connection_id')->references('connection_id')->on('ServiceConnection')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('user_id');
            $table->index('reading_date');

            // Unique constraint to prevent duplicate uploads for same schedule and connection
            // Note: This also serves as an index on (schedule_id, connection_id)
            $table->unique(['schedule_id', 'connection_id'], 'unique_schedule_connection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaded_readings');
    }
};
