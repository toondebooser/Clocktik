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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string("company_name")->nullable();
            $table->string('company_logo')->nullable();
            $table->string('company_color')->default("#4FAAFC");
            $table->bigInteger('company_code')->unsigned();
            $table->index('company_code');
            $table->integer('weekend_day_1')->default(6); 
            $table->integer('weekend_day_2')->default(0); 
            $table->decimal('day_hours', 5, 2)->default(7.6);
            $table->boolean('admin_timeclock')->default(false);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
