<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChallengeParticipants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('challenges_participants', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('challenges_id');
            $table->integer('users_id');
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->index('challenges_id');
            $table->index('users_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('challenges_participants');
    }
}
