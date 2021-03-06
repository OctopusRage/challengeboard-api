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
            $table->integer('challenge_id');
            $table->integer('user_id');
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->index('challenge_id');
            $table->index('user_id');
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
