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
        Schema::create('blog', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('author');
            $table->foreignUuid('categories');
            $table->foreignUuid('comments')->nullable();
            $table->string('title');
            $table->text('content');
            $table->json('image');
            $table->integer('like')->default(0);
            $table->timestamps();

            $table->foreign('author')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
  
            $table->foreign('categories')
            ->references('id')
            ->on('categories')
            ->onDelete('cascade');
        
            $table->foreign('comments')
            ->references('id')
            ->on('comment')
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
        Schema::dropIfExists('blog');
    }
};
