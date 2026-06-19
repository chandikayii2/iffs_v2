<?php
// database/migrations/2024_01_01_000006_create_refilling_order_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefillingOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('refilling_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refilling_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('tire_id')->constrained()->onDelete('cascade');
            $table->decimal('refilling_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('refilling_order_items');
    }
}