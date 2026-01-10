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
        Schema::create('CustomerLedger', function (Blueprint $table) {
            $table->id('ledger_entry_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('connection_id')->nullable();
            $table->unsignedBigInteger('period_id')->nullable();
            $table->date('txn_date');
            $table->datetime('post_ts', 6)->useCurrent();
            $table->enum('source_type', ['BILL', 'CHARGE', 'ADJUST', 'PAYMENT', 'REFUND', 'WRITE_OFF', 'TRANSFER', 'REVERSAL']);
            $table->unsignedBigInteger('source_id');
            $table->integer('source_line_no')->nullable();
            $table->integer('source_line_no_nz')->storedAs('IFNULL(source_line_no, 0)');
            $table->string('description', 200)->nullable();
            $table->decimal('debit', 12, 2)->default(0.00);
            $table->decimal('credit', 12, 2)->default(0.00);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('stat_id');

            // Foreign keys (will be added in a separate migration after all tables are created)

            // Indexes
            $table->index('customer_id');
            $table->index('connection_id');
            $table->index('period_id');
            $table->index('source_type');
            $table->index('source_id');
            $table->index(['source_type', 'source_id']);
            $table->index('txn_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CustomerLedger');
    }
};
