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
        Schema::create('Usertotals', function (Blueprint $table) {
            $table->id();
            $table->integer('UserId');
            $table->decimal('RegularHours',5,2);
            $table->decimal('BreakHours',5,2);
            $table->decimal('OverTimes',5,2);
            $table->date('Month');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Usertotals');
    }
};
