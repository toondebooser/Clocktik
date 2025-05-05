<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {

        Schema::create('extra_break_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('timesheet_id');
            $table->index('timesheet_id');
            $table->unsignedBigInteger('daytotal_id');
            $table->index('daytotal_id');
            $table->unsignedBigInteger('UserId');
            $table->index('UserId');
            $table->timestamp('BreakStart')->nullable();
            $table->timestamp('BreakStop')->nullable();
            $table->string('type')->default('workday');
            $table->date('Month');
            $table->index('Month');
            $table->timestamps();
            $table->foreign('daytotal_id')
                ->references('id')
                ->on('daytotals')
                ->onDelete('cascade');

            $table->foreign('timesheet_id')
                ->references('id')
                ->on('timesheets')
                ->onDelete('cascade');
                
            $table->foreign('UserId')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('extra_break_slots');
    }
};
