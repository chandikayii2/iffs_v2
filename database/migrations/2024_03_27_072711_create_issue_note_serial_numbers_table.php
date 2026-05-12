<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issue_note_serial_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('issue_note_id');
            $table->foreign('issue_note_id')->references('id')->on('issue_notes');
            $table->unsignedInteger('issue_note_product_id');
            $table->foreign('issue_note_product_id')->references('id')->on('issue_note_products');
            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->integer('serial_number_id');
            $table->string('serial_number');
            $table->boolean('is_active')->default(1);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_note_serial_numbers');
    }
};
