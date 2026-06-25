<?php
// database/migrations/2024_01_01_000012_drop_expected_return_date_from_refilling_orders.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropExpectedReturnDateFromRefillingOrders extends Migration
{
    public function up()
    {
        Schema::table('refilling_orders', function (Blueprint $table) {
            if (Schema::hasColumn('refilling_orders', 'expected_return_date')) {
                $table->dropColumn('expected_return_date');
            }
        });
    }

    public function down()
    {
        Schema::table('refilling_orders', function (Blueprint $table) {
            $table->date('expected_return_date')->nullable();
        });
    }
}