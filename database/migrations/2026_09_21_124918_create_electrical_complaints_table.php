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
        Schema::create('electrical_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('building');
            $table->string('room');
            $table->string('phone');
            $table->text('complaint');
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->boolean('done')->default(false);
            $table->text('remark')->nullable();
            $table->date('action_date')->nullable();
            $table->text('user_reply')->nullable();
            $table->text('signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electrical_complaints');
    }
};
