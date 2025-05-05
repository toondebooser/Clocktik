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
        Schema::create('daytotals', function (Blueprint $table) {
            $table->id();
            $table->integer('UserId');
            $table->index('UserId');
            $table->integer('DaytimeCount')->default(1);
            $table->decimal('RegularHours', 5, 2)->default(0.00); // Fixed
            $table->decimal('accountableHours', 5, 2)->default(0.00); // Fixed
            $table->integer('BreaksTaken')->default(0);
            $table->decimal('BreakHours', 5, 2)->default(0.00); // Fixed
            $table->decimal('OverTime', 5, 2)->default(0.00); // Fixed
            $table->string('type')->default('workday');
            $table->boolean('userNote')->default(false);
            $table->date('Month');
            $table->index('Month');
            $table->boolean('official_holiday')->default(false);
            $table->boolean('Completed')->default(false);
            $table->boolean('Weekend')->default(false);
            $table->boolean('NightShift')->default(false);
            $table->boolean('DayOverlap')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daytotals');
    }
};
