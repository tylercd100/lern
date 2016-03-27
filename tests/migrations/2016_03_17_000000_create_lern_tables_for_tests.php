<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLERNTablesForTests extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('lern.record.table'), function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('class');
            $table->string('file');
            $table->integer('code');
            $table->integer('status_code')->default(0);
            $table->integer('line');
            $table->text('message');
            $table->mediumText('trace');
            $table->timestamps();
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
        Schema::drop(config('lern.record.table'));
    }

}