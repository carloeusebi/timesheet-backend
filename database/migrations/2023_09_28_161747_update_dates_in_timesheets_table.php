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
        Schema::table('timesheets', function (Blueprint $table) {
            $table->addColumn('datetime', 'activity_start')->after('activity_id')->nullable();
            $table->addColumn('datetime', 'activity_end')->after('activity_start')->nullable();
            $table->dropColumn('date');
            $table->dropColumn('hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timesheets', function (Blueprint $table) {
            $table->dropColumn('activity_start');
            $table->dropColumn('activity_end');
            $table->addColumn('date', 'date')->after('activity_id')->nullable();
            $table->addColumn('tinyInteger', 'hours')->after('date')->nullable();
        });
    }
};
