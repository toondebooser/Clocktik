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
            $table->boolean('BreakStatus');
            $table->boolean('ShiftStatus');
            $table->boolean('Weekend');
            $table->boolean('NightShift')->default(false); 
            $table->timestamp('StartWork')->nullable();
            $table->timestamp('StartBreak')->nullable();
            $table->timestamp('EndBreak')->nullable();
            $table->timestamp('StopWork')->nullable();
            $table->integer('BreaksTaken')->default(0);
            $table->decimal('BreakHours', 5, 2)->default(0, 00);
            $table->decimal('RegularHours', 5, 2)->default(0, 00);
            $table->text('userNote')->nullable();
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
