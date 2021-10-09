<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('senderId');
            $table->unsignedBigInteger('receiverId');
            $table->timestamps();
            $table->foreign('senderId')->on('contacts')->references('id');
            $table->foreign('receiverId')->on('contacts')->references('id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversations');
    }
}
