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
        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id();
            $table->string('building');
            $table->string('room');
            $table->string('name_on_bill')->nullable();
            $table->string('in_id')->nullable();
            $table->string('meter_number')->nullable();
            $table->string('consumer_id')->nullable();
            $table->string('account_no')->nullable();
            $table->string('meter_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
