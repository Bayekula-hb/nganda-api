<?php

use App\Models\User;
use Carbon\Carbon;
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
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('address');
            $table->string('pos')->nullable();
            $table->string('numberPos')->max(12)->nullable();
            $table->json('workers')->nullable();
            $table->json('workingDays')->nullable();
            $table->boolean('isOnDemonstration')->default(true);
            $table->date('subscriptionExpiryDate')->nullable()->default(Carbon::now()->addWeeks(2));
            $table->json('settings')->nullable();
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
