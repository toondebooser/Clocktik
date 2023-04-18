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
        Schema::create('TimeSheets', function (Blueprint $table) {
            $table->id();
            $table->integer('UserId');
            $table->timestamp('ClockedIn');
            $table->timestamp('ClockedOut');
            $table->timestamp('BreakStart')->nullable();
            $table->timestamp('BreakStop')->nullable();
            $table->integer('RegularHours');
            $table->integer('BreakHours')->nullable();
            $table->integer('OverTime')->nullable();
            $table->date('Month');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TimeSheets');
    }
};
