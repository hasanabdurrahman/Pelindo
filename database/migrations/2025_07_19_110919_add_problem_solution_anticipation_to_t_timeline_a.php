<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('t_timelineA', function (Blueprint $table) {
        $table->text('problem')->nullable();
        $table->text('solution')->nullable();
        $table->text('anticipation')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('t_timelineA', function (Blueprint $table) {
        $table->dropColumn(['problem','solution','anticipation']);
    });
}
};
