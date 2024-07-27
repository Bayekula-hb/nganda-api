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
        Schema::create('drinks', function (Blueprint $table) {
            $table->id();
            $table->string('nameDrink');
            $table->string('imageDrink')->nullable(true);
            $table->string('litrage')->nullable(true);
            $table->string('typeDrink')->nullable(true);
            $table->integer('priorityDrink')->nullable(false)->default(0);
            $table->integer('numberBottle')->nullable(false)->default(12);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drinks');
    }
};
