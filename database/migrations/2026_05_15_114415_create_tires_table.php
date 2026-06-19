<?php
// database/migrations/2024_01_01_000001_create_tires_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiresTable extends Migration
{
    public function up()
    {
        Schema::create('tires', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('brand');
            $table->string('size');
            $table->string('type'); // Radial, Bias, etc.
            $table->string('status')->default('new'); // new, in_use, used, at_vendor, scrap
            $table->integer('refill_count')->default(0);
            $table->integer('max_refills')->default(3);
            $table->string('current_location')->nullable(); // vehicle_id or vendor
            $table->date('purchase_date');
            $table->decimal('purchase_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tires');
    }
}