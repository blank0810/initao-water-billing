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
            Schema::create('meter_reading_old', function (Blueprint $table) {
                  $table->id('mro_id');
                  $table->unsignedBigInteger('connection_id');
                  $table->unsignedBigInteger('meter_id');
                  $table->decimal('reading', 10, 2);
                  $table->date('reading_date');
                  $table->unsignedBigInteger('reader_id');
                  $table->text('notes')->nullable();
                  $table->unsignedBigInteger('stat_id');
                  $table->unsignedBigInteger('user_id');
                  $table->timestamps();

                  // Foreign key constraints (will be added in a separate migration after all tables are created)

                  // Add index for search optimization
                  $table->index('connection_id', 'meter_reading_old_connection_index');
                  $table->index('reading_date', 'meter_reading_old_date_index');
                  $table->index('meter_id', 'meter_reading_old_meter_index');
            });
      }

      /**
       * Reverse the migrations.
       */
      public function down(): void
      {
            Schema::dropIfExists('meter_reading_old');
      }
};
