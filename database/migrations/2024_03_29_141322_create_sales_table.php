<?php

use App\Models\establishment;
use App\Models\inventoryDrink;
use App\Models\User;
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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');            
            $table->foreignIdFor(User::class)
                ->references('id')
                ->on('users');
            $table->foreignIdFor(establishment::class)
                ->references('id')
                ->on('establishments');
            $table->foreignIdFor(inventoryDrink::class)
                ->references('id')
                ->on('inventory_drinks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
