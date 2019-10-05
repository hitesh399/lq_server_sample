<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_config', function (Blueprint $table) {
            //
            $table->enum('config_type', ['global', 'private'])->default('global');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_config', function (Blueprint $table) {
            //
            $table->dropColumn('config_type');
        });
    }
}
