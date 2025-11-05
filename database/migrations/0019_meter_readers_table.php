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
        Schema::create('meter_readers', function (Blueprint $table) {
            $table->id('mr_id');
            $table->string('mr_name');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');

            // Add index for search optimization
            $table->index('mr_name', 'meter_reader_name_index');
        });

        // Insert default meter readers
        $now = now();
        $meterReaders = [
            [
                'mr_name' => 'John Doe',
                'contact_number' => '09123456789',
                'email' => 'john.doe@example.com',
                'stat_id' => 2, // ACTIVE
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'mr_name' => 'Jane Smith',
                'contact_number' => '09123456780',
                'email' => 'jane.smith@example.com',
                'stat_id' => 2, // ACTIVE
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('meter_readers')->insert($meterReaders);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meter_readers');
    }
};
