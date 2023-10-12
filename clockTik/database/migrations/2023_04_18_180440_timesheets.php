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
            $table->timestamp('ClockedIn')->nullable();
            $table->timestamp('ClockedOut')->nullable();
            $table->json('DayShifts')->default(json_encode([]))->nullable();
            $table->timestamp('BreakStart')->nullable();
            $table->timestamp('BreakStop')->nullable();
            $table->decimal('RegularHours',5,2)->default(0,00);
            $table->decimal('accountableHours')->default(0,00);
            $table->decimal('BreakHours',5,2)->default(0,00);
            $table->decimal('OverTime',5,2)->default(0,00);
            $table->string('type')->default('workday');
            $table->text('userNote')->nullable();
            $table->date('Month');
            $table->boolean('Weekend')->default(false);
            $table->boolean('MultipleShifts')->default(false);
         
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
