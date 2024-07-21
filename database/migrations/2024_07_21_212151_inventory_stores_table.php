<?php

use App\Models\drink;
use App\Models\establishment;
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
        Schema::create('inventory_stores', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity')->nullable(false);
            $table->double('price')->nullable(false);
            $table->foreignIdFor(drink::class)
                ->references('id')
                ->on('drinks');
            $table->foreignIdFor(establishment::class)
                ->references('id')
                ->on('establishments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stores');
    }
};
