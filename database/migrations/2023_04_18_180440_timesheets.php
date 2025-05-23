<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daytotal_id');
            $table->index('daytotal_id');
            $table->unsignedBigInteger('UserId');
            $table->index('UserId');
            $table->timestamp('ClockedIn')->nullable();
            $table->timestamp('ClockedOut')->nullable();
            $table->timestamp('BreakStart')->nullable();
            $table->timestamp('BreakStop')->nullable();
            $table->text('userNote')->nullable();
            $table->string('type')->default('workday');
            $table->date('Month');
            $table->index('Month');
            $table->timestamps();
            $table->foreign('daytotal_id')
                ->references('id')
                ->on('daytotals')
                ->onDelete('cascade');

            $table->foreign('UserId')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timesheets');
    }
};
