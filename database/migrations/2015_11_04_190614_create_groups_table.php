<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->mediumInteger('id', true);
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestampsTz();
            $table->integer('leader_id')->nullable();
            $table->tinyInteger('partner')->default(0);
            $table->tinyInteger('system')->default(0);

            $table->foreign('leader_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('groups');
    }
}
