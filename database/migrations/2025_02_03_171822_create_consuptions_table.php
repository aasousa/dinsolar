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
        Schema::create('consuptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('residence_id')->constrained();
            $table->date('date');
            $table->integer('kwh');
            $table->float('te', 5)->nullable();
            $table->float('tusd', 5)->nullable();
            $table->float('ammount', 2)->nullable();
            $table->enum('flag', ['green', 'yellow', 'red_1', 'red_2'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consuptions');
    }
};
