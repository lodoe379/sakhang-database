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
        Schema::create('room_furnitures', function (Blueprint $table) {
            $table->id();
            $table->string('building');
            $table->string('room');
            $table->string('bed')->nullable();
            $table->string('table')->nullable();
            $table->string('chair')->nullable();
            $table->string('cupboard')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_furnitures');
    }
};
