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
        Schema::table('tires', function (Blueprint $table) {
            if (!Schema::hasColumn('tires', 'total_refill_count')) {
                $table->integer('total_refill_count')->default(0);
            }
            if (!Schema::hasColumn('tires', 'rounds_finished')) {
                $table->integer('rounds_finished')->default(0);
            }
            if (!Schema::hasColumn('tires', 'round_kms')) {
                $table->json('round_kms')->nullable();
            }
            if (!Schema::hasColumn('tires', 'tire_type')) {
                $table->enum('tire_type', ['original', 'original_casing', 'dag_used'])->nullable();
            }
            if (!Schema::hasColumn('tires', 'is_refilled')) {
                $table->boolean('is_refilled')->default(false);
            }
        });

        Schema::table('tire_allocations', function (Blueprint $table) {
            if (!Schema::hasColumn('tire_allocations', 'consumed_mileage')) {
                $table->integer('consumed_mileage')->default(0);
            }
            if (!Schema::hasColumn('tire_allocations', 'remark')) {
                $table->text('remark')->nullable();
            }
        });

        Schema::table('refilling_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('refilling_orders', 'payment_method')) {
                $table->enum('payment_method', ['cash', 'online', 'check'])->nullable();
            }
            if (!Schema::hasColumn('refilling_orders', 'payment_reference')) {
                $table->string('payment_reference', 100)->nullable();
            }
            if (!Schema::hasColumn('refilling_orders', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending');
            }
            if (!Schema::hasColumn('refilling_orders', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0.00);
            }
        });

        Schema::table('tire_scrap_records', function (Blueprint $table) {
            if (!Schema::hasColumn('tire_scrap_records', 'scrap_category')) {
                $table->enum('scrap_category', ['store', 'kurunagala', 'sold'])->default('store');
            }
            if (!Schema::hasColumn('tire_scrap_records', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('tire_scrap_records', 'buyer_name')) {
                $table->string('buyer_name', 255)->nullable();
            }
            if (!Schema::hasColumn('tire_scrap_records', 'sale_payment_method')) {
                $table->enum('sale_payment_method', ['cash', 'online', 'check'])->nullable();
            }
            if (!Schema::hasColumn('tire_scrap_records', 'sale_payment_status')) {
                $table->enum('sale_payment_status', ['pending', 'paid'])->default('pending');
            }
            if (!Schema::hasColumn('tire_scrap_records', 'sale_reference')) {
                $table->string('sale_reference', 100)->nullable();
            }
            if (!Schema::hasColumn('tire_scrap_records', 'sale_date')) {
                $table->date('sale_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tires', function (Blueprint $table) {
            $table->dropColumn(['total_refill_count', 'rounds_finished', 'round_kms', 'tire_type', 'is_refilled']);
        });

        Schema::table('tire_allocations', function (Blueprint $table) {
            $table->dropColumn(['consumed_mileage', 'remark']);
        });

        Schema::table('refilling_orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_reference', 'payment_status', 'paid_amount']);
        });

        Schema::table('tire_scrap_records', function (Blueprint $table) {
            $table->dropColumn([
                'scrap_category', 'sale_price', 'buyer_name', 'sale_payment_method', 
                'sale_payment_status', 'sale_reference', 'sale_date'
            ]);
        });
    }
};
