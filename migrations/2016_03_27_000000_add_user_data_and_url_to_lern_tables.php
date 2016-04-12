<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddUserDataAndUrlToLernTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('lern.record.table'), function(Blueprint $table) {
            $table->integer('user_id')->nullable();
            $table->text('data')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('lern.record.table'), function(Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('data');
            $table->dropColumn('url');
            $table->dropColumn('method');
        });
    }

}