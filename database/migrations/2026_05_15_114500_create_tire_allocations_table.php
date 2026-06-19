<?php
// database/migrations/2024_01_01_000003_create_tire_allocations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTireAllocationsTable extends Migration
{
    public function up()
    {
        Schema::create('tire_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tire_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->integer('mileage_at_installation');
            $table->integer('mileage_at_removal')->nullable();
            $table->string('position')->nullable(); // Front Left, Front Right, etc.
            $table->date('installation_date');
            $table->date('removal_date')->nullable();
            $table->text('removal_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tire_allocations');
    }
}