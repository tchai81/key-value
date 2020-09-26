<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeyValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_values', function (Blueprint $table) {
            $table->id();
            $table->integer('key_id')->unsigned();
            $table->string('value')->nullable(false);
            $table->timestamps();
        });

        // Schema::table('key_values', function($table) {
        //     $table->foreign('key_id')->references('id')->on('keys');
        // });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('key_values');
    }
}
