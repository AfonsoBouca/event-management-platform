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
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->string('eventname');
            $table->text('description');
            $table->dateTime('startdate');
            $table->dateTime('enddate');
            $table->tinyInteger('capacity');
            $table->unsignedMediumInteger('price');
            $table->boolean('ispublic');
            $table->string('location');
            $table->foreignId('createdby')->constrained('user')->onDelete('cascade');
            $table->enum('eventstatus', ['Upcoming', 'Ongoing', 'Completed', 'Cancelled'])->default('U  pcoming');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
