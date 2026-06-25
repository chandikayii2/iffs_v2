<?php
// database/migrations/2024_01_01_000009_add_consumption_mileage_to_tires.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsumptionMileageToTires extends Migration
{
    public function up()
    {
        Schema::table('tires', function (Blueprint $table) {
            if (!Schema::hasColumn('tires', 'consumption_mileage')) {
                $table->integer('consumption_mileage')->default(0)->after('purchase_price');
            }
            if (!Schema::hasColumn('tires', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->constrained('refilling_vendors')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('tires', function (Blueprint $table) {
            $table->dropColumn(['consumption_mileage']);
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['vendor_id']);
        });
    }
}