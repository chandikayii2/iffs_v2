<?php
// database/migrations/2024_01_01_000005_create_refilling_orders_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefillingOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('refilling_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('vendor_id')->constrained('refilling_vendors')->onDelete('cascade');
            $table->date('sent_date');
            $table->date('expected_return_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('status')->default('sent'); // sent, processing, received
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('refilling_orders');
    }
}