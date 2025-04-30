<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        
        Schema::create('timesheets_break', function (Blueprint $table) {
            $table->id();
            $table->integer('daytotal_id');
            $table->index('daytotal_id');
            $table->integer('UserId');
            $table->index('UserId');
            $table->timestamp('BreakStart')->nullable();
            $table->timestamp('BreakStop')->nullable();
            $table->date('Month');
            $table->index('Month');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('timesheets_break');

    }
};
