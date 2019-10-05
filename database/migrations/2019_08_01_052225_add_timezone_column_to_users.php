<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimezoneColumnToUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone')->nullable();
        });
        Schema::table('device_user', function (Blueprint $table) {
            $table->string('timezone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('timezone');
        });
        Schema::table('device_user', function (Blueprint $table) {
            $table->dropColumn('timezone');
        });
    }
}
