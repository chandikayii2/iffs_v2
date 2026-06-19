<?php
// database/migrations/2024_01_01_000010_create_tire_issue_notes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTireIssueNotesTable extends Migration
{
    public function up()
    {
        Schema::create('tire_issue_notes', function (Blueprint $table) {
            $table->id();
            $table->string('issue_note_number')->unique();
            $table->date('issue_date');
            $table->text('remarks')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('tire_issue_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tire_issue_note_id')->constrained()->onDelete('cascade');
            $table->foreignId('tire_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('consumed_mileage')->default(0);
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tire_issue_note_items');
        Schema::dropIfExists('tire_issue_notes');
    }
}