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
            $table->bigInteger('company_code',10)->unsigned();
            $table->index('company_code');
            $table->integer('DaytimeCount')->default(1);
            $table->decimal('RegularHours', 5, 2)->default(0, 00);
            $table->decimal('accountableHours', 5, 2)->default(0, 00);
            $table->integer('BreaksTaken')->default(0);
            $table->decimal('BreakHours', 5, 2)->default(0, 00);
            $table->decimal('OverTime', 5, 2)->default(0, 00);
            $table->string('type')->default('workday');
            $table->text('userNote')->nullable();
            $table->date('Month');
            $table->index('Month');
            $table->boolean('Weekend')->default(false);
            $table->boolean('NightShift')->default(false);
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
