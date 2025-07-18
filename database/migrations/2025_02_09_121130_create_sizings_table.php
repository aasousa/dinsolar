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
        Schema::create('sizings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('residence_id')->constrained();
            $table->string('name');
            $table->integer('days');
            $table->decimal('hours', 5, 2);
            $table->decimal('kw', 6, 3);
            $table->decimal('kwh', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizings');
    }
};
