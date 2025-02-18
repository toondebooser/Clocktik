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
        Schema::create('weektypes', function (Blueprint $table) {
            $table->id();
            $table->json('weekend_days')->default(json_encode([0, 6]));            
            $table->string('CompanyCode', 10);
            $table->index('CompanyCode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weektypes');
    }
};
