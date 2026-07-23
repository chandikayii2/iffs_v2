<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refilling_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('refilling_orders', 'payment_date')) {
                $table->date('payment_date')->nullable();
            }
            if (!Schema::hasColumn('refilling_orders', 'payment_notes')) {
                $table->text('payment_notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refilling_orders', function (Blueprint $table) {
            $table->dropColumn(['payment_date', 'payment_notes']);
        });
    }
};
