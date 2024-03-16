<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('author');
            $table->foreignUuid('blog');
            $table->string('content');
            $table->string('like');
            $table->timestamps();

            $table->foreign('author')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
  
            $table->foreign('blog')
            ->references('id')
            ->on('blog')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment');
    }
};
