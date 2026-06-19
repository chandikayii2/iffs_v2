<?php
// database/migrations/2024_01_01_000004_create_refilling_vendors_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefillingVendorsTable extends Migration
{
    public function up()
    {
        Schema::create('refilling_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('refilling_vendors');
    }
}