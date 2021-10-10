<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
//            $table->unsignedBigInteger('support_id')->nullable();
//            $table->foreign('support_id')
//                ->references('id')
//                ->on('users')
//                ->onDelete('cascade')
//                ->onUpdate('cascade');
//            $table->unsignedBigInteger('receive_id')->nullable();
//            $table->foreign('receive_id')
//                ->references('id')
//                ->on('users')
//                ->onDelete('cascade')
//                ->onUpdate('cascade');
//            $table->foreignId('user_id')->nullable()
//                ->constrained()
//                ->onDelete('cascade')
//                ->onUpdate('cascade');
//            $table->foreignId('guest_id')->nullable()
//                ->default(null)
//                ->constrained()
//                ->onDelete('cascade')
//                ->onUpdate('cascade');
            $table->text('Message');
            $table->timestamps();
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
        Schema::dropIfExists('messages');
    }
}
