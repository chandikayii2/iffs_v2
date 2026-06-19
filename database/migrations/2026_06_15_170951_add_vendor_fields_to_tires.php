<?php
// database/migrations/2024_01_01_000009_add_vendor_fields_to_tires.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorFieldsToTires extends Migration
{
    public function up()
    {
        Schema::table('tires', function (Blueprint $table) {
            if (!Schema::hasColumn('tires', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->constrained('refilling_vendors')->onDelete('set null');
            }
            if (!Schema::hasColumn('tires', 'consumption_mileage')) {
                $table->integer('consumption_mileage')->default(0);
            }
        });
        
        Schema::table('tire_allocations', function (Blueprint $table) {
            if (!Schema::hasColumn('tire_allocations', 'remark')) {
                $table->text('remark')->nullable();
            }
            if (!Schema::hasColumn('tire_allocations', 'consumed_mileage')) {
                $table->integer('consumed_mileage')->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('tires', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['vendor_id', 'consumption_mileage']);
        });
        
        Schema::table('tire_allocations', function (Blueprint $table) {
            $table->dropColumn(['remark', 'consumed_mileage']);
        });
    }
}