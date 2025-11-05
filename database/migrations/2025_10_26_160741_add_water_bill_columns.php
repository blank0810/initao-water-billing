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
        Schema::table('water_bill', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('water_bill', 'bill_number')) {
                $table->string('bill_number')->nullable()->unique()->after('wb_id');
            }
            
            if (!Schema::hasColumn('water_bill', 'connection_id')) {
                $table->unsignedBigInteger('connection_id')->nullable()->after('bill_number');
            }
            
            if (!Schema::hasColumn('water_bill', 'reading_id')) {
                $table->unsignedBigInteger('reading_id')->nullable()->after('per_id');
            }
            
            // Add other new columns with appropriate data types and defaults
            if (!Schema::hasColumn('water_bill', 'previous_reading')) {
                $table->decimal('previous_reading', 10, 2)->default(0)->after('reading_id');
            }
            
            if (!Schema::hasColumn('water_bill', 'current_reading')) {
                $table->decimal('current_reading', 10, 2)->default(0)->after('previous_reading');
            }
            
            if (!Schema::hasColumn('water_bill', 'consumption')) {
                $table->decimal('consumption', 10, 2)->default(0)->after('current_reading');
            }
            
            if (!Schema::hasColumn('water_bill', 'rate_id')) {
                $table->unsignedBigInteger('rate_id')->nullable()->after('consumption');
            }
            
            if (!Schema::hasColumn('water_bill', 'rate_amount')) {
                $table->decimal('rate_amount', 10, 2)->default(0)->after('rate_id');
            }
            
            if (!Schema::hasColumn('water_bill', 'basic_charge')) {
                $table->decimal('basic_charge', 10, 2)->default(0)->after('rate_amount');
            }
            
            if (!Schema::hasColumn('water_bill', 'surcharge')) {
                $table->decimal('surcharge', 10, 2)->default(0)->after('basic_charge');
            }
            
            if (!Schema::hasColumn('water_bill', 'discount')) {
                $table->decimal('discount', 10, 2)->default(0)->after('surcharge');
            }
            
            if (!Schema::hasColumn('water_bill', 'penalty')) {
                $table->decimal('penalty', 10, 2)->default(0)->after('discount');
            }
            
            if (!Schema::hasColumn('water_bill', 'misc_charges')) {
                $table->decimal('misc_charges', 10, 2)->default(0)->after('penalty');
            }
            
            if (!Schema::hasColumn('water_bill', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('misc_charges');
            }
            
            if (!Schema::hasColumn('water_bill', 'billing_date')) {
                $table->date('billing_date')->nullable()->after('total_amount');
            }
            
            if (!Schema::hasColumn('water_bill', 'due_date')) {
                $table->date('due_date')->nullable()->after('billing_date');
            }
            
            if (!Schema::hasColumn('water_bill', 'payment_status')) {
                $table->enum('payment_status', ['unpaid', 'partially_paid', 'paid', 'overdue', 'cancelled'])->default('unpaid')->after('due_date');
            }
            
            if (!Schema::hasColumn('water_bill', 'paid_date')) {
                $table->date('paid_date')->nullable()->after('payment_status');
            }
            
            if (!Schema::hasColumn('water_bill', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0)->after('paid_date');
            }
            
            if (!Schema::hasColumn('water_bill', 'balance')) {
                $table->decimal('balance', 10, 2)->default(0)->after('amount_paid');
            }
            
            // Add created_by if it doesn't exist
            if (!Schema::hasColumn('water_bill', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('stat_id');
            }
            
            if (!Schema::hasColumn('water_bill', 'cancelled_by')) {
                $table->unsignedBigInteger('cancelled_by')->nullable()->after('created_by');
            }
            
            if (!Schema::hasColumn('water_bill', 'cancelled_at')) {
                $table->dateTime('cancelled_at')->nullable()->after('cancelled_by');
            }
            
            if (!Schema::hasColumn('water_bill', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }
        });
        
        // Add foreign key constraints after all columns are added
        Schema::table('water_bill', function (Blueprint $table) {
            // Add foreign key for service_connection
            if (Schema::hasColumn('water_bill', 'connection_id') && 
                Schema::hasTable('service_connection') &&
                !Schema::hasColumn('water_bill', 'water_bill_connection_id_foreign')) {
                $table->foreign('connection_id')
                      ->references('connection_id')
                      ->on('service_connection')
                      ->onDelete('restrict');
            }
            
            // Add foreign key for meter_reading
            if (Schema::hasColumn('water_bill', 'reading_id') && 
                Schema::hasTable('meter_reading') &&
                !Schema::hasColumn('water_bill', 'water_bill_reading_id_foreign')) {
                $table->foreign('reading_id')
                      ->references('reading_id')
                      ->on('meter_reading')
                      ->onDelete('restrict');
            }
            
            // Add foreign key for water_rate
            if (Schema::hasColumn('water_bill', 'rate_id') && 
                Schema::hasTable('water_rate') &&
                !Schema::hasColumn('water_bill', 'water_bill_rate_id_foreign')) {
                $table->foreign('rate_id')
                      ->references('rate_id')
                      ->on('water_rate')
                      ->onDelete('restrict');
            }
            
            // Add foreign key for cancelled_by (users table)
            if (Schema::hasColumn('water_bill', 'cancelled_by') && 
                Schema::hasTable('users') &&
                !Schema::hasColumn('water_bill', 'water_bill_cancelled_by_foreign')) {
                $table->foreign('cancelled_by')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');
            }
            
            // Add indexes for search optimization
            if (Schema::hasColumn('water_bill', 'bill_number') && 
                !Schema::hasIndex('water_bill', 'water_bill_bill_number_index')) {
                $table->index('bill_number', 'water_bill_bill_number_index');
            }
            
            if (Schema::hasColumn('water_bill', 'connection_id') && 
                !Schema::hasIndex('water_bill', 'water_bill_connection_index')) {
                $table->index('connection_id', 'water_bill_connection_index');
            }
            
            if (Schema::hasColumn('water_bill', 'per_id') && 
                !Schema::hasIndex('water_bill', 'water_bill_period_index')) {
                $table->index('per_id', 'water_bill_period_index');
            }
            
            if (Schema::hasColumn('water_bill', 'payment_status') && 
                !Schema::hasIndex('water_bill', 'water_bill_payment_status_index')) {
                $table->index('payment_status', 'water_bill_payment_status_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys and columns added in the up() method
        Schema::table('water_bill', function (Blueprint $table) {
            // Drop foreign keys if they exist
            $foreignKeys = [
                'water_bill_connection_id_foreign',
                'water_bill_reading_id_foreign',
                'water_bill_rate_id_foreign',
                'water_bill_cancelled_by_foreign'
            ];
            
            foreach ($foreignKeys as $foreignKey) {
                if (Schema::hasColumn('water_bill', str_replace('water_bill_', '', str_replace('_foreign', '', $foreignKey)))) {
                    $table->dropForeign([str_replace('water_bill_', '', str_replace('_foreign', '', $foreignKey))]);
                }
            }
            
            // Drop indexes if they exist
            $indexes = [
                'water_bill_bill_number_index',
                'water_bill_connection_index',
                'water_bill_period_index',
                'water_bill_payment_status_index'
            ];
            
            foreach ($indexes as $index) {
                if (Schema::hasIndex('water_bill', $index)) {
                    $table->dropIndex($index);
                }
            }
            
            // Drop columns if they exist
            $columnsToDrop = [
                'bill_number',
                'connection_id',
                'reading_id',
                'previous_reading',
                'current_reading',
                'consumption',
                'rate_id',
                'rate_amount',
                'basic_charge',
                'surcharge',
                'discount',
                'penalty',
                'misc_charges',
                'total_amount',
                'billing_date',
                'due_date',
                'payment_status',
                'paid_date',
                'amount_paid',
                'balance',
                'cancelled_by',
                'cancelled_at',
                'cancellation_reason'
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('water_bill', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
