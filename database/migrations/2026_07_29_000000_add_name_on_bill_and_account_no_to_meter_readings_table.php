<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            if (!Schema::hasColumn('meter_readings', 'name_on_bill')) {
                $table->string('name_on_bill')->nullable();
            }
            if (!Schema::hasColumn('meter_readings', 'account_no')) {
                $table->string('account_no')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropColumn(['name_on_bill', 'account_no']);
        });
    }
};
