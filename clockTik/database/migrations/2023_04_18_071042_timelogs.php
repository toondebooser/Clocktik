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
        Schema::create('Timelogs', function (Blueprint $table) {
            $table->id();
            $table->boolean('BreakStatus');
            $table->boolean('ShiftStatus');
            $table->boolean('Weekend');
            $table->boolean('SecondShift');
            $table->timestamp('StartWork')->nullable();
            $table->timestamp('StartBreak')->nullable();
            $table->timestamp('EndBreak')->nullable();
            $table->timestamp('StopWork')->nullable();
            $table->integer('UserId');
            $table->timestamps();
        });
        
        }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Timelogs');
    }
};
