<?php
// database/migrations/2024_01_01_000007_create_tire_scrap_records_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTireScrapRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('tire_scrap_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tire_id')->constrained()->onDelete('cascade');
            $table->date('scrap_date');
            $table->string('scrap_reason');
            $table->integer('final_mileage')->nullable();
            $table->string('disposal_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tire_scrap_records');
    }
}