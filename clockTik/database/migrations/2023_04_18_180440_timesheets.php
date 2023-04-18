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
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->integer('UserId');
            $table->timestamp('ClockedIn');
            $table->timestamp('ClockedOut');
            $table->timestamp('BreakStart');
            $table->timestamp('BreakStop');
            $table->decimal('RegularHours',5,2);
            $table->decimal('BreakHours',5,2);
            $table->decimal('OverTime',5,2);
            $table->date('Month');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timesheets');
    }
};
