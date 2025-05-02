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
        Schema::create('timelogs', function (Blueprint $table) {
            $table->id();
            $table->boolean('BreakStatus')->default(false);
            $table->boolean('ShiftStatus')->default(false);
            $table->boolean('Weekend')->default(false);
            $table->integer('timesheet_id')->nullable();
            $table->index('timesheet_id');
            $table->boolean('NightShift')->default(false); 
            $table->timestamp('StartWork')->nullable();
            $table->timestamp('StartBreak')->nullable();
            $table->timestamp('EndBreak')->nullable();
            $table->timestamp('StopWork')->nullable();
            $table->integer('BreaksTaken')->default(0);
            $table->text('userNote')->nullable();
            $table->integer('daytotal_id')->nullable();
            $table->integer('UserId');
            $table->index('UserId');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timelogs');
    }
};
