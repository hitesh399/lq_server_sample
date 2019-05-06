<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMobileNoInUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('mobile_no', 20)->nullable()->unique()->after('email_verified_at');
            $table->timestamp('mobile_no_verified_at')->nullable()->after('mobile_no');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('mobile_no');
            $table->dropColumn('mobile_no_verified_at');
            $table->dropSoftDeletes();
        });
    }
}
