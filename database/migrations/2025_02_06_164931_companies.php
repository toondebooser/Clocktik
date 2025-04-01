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
            $table->string('image')->nullable();
            $table->string('color')->nullable();
            $table->bigInteger('company_code')->unsigned();
            $table->index('company_code');
            $table->string('weekend_day_1')->default("Zaterdag"); 
            $table->string('weekend_day_2')->default("Zondag"); 
            $table->decimal('day_hours', 5, 2)->default(7.6);
            $table->boolean('Admin_timeclock')->default(false);
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
