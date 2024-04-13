<?php

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
        Schema::create('establishments', function (Blueprint $table) {
            $table->id();
            $table->string('nameEtablishment');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('address');
            $table->string('pos')->nullable();
            $table->string('numberPos')->max(12)->nullable();
            $table->json('workers')->nullable();
            $table->foreignIdFor(User::class)
                ->references('id')
                ->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establishments');
    }
};
