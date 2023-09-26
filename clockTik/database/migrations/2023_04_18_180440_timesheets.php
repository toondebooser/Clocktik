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
            $table->decimal('RegularHours',5,2)->default(0,00);
            $table->decimal('accountableHours')->default(0,00);
            $table->decimal('BreakHours',5,2)->default(0,00);
            $table->decimal('OverTime',5,2)->default(0,00);
            $table->date('Month');
            $table->boolean('Weekend')->default(false);
            $table->boolean('ziek')->default(false);
            $table->boolean('weerverlet')->default(false);
            $table->boolean('onbetaald')->default(false);
            $table->boolean('feestdag')->default(false);
            $table->boolean('vakantie')->default(false);
            $table->boolean('solicitatie')->default(false);
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
