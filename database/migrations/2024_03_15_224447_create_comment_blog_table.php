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
        Schema::create('comment_blog', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('blog_id')->unique();
            $table->foreignUuid('comment_id');
            $table->timestamps();
    
            $table->foreign('blog_id')
                ->references('id')
                ->on('blog')
                ->onDelete('cascade');
    
            $table->foreign('comment_id')
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
        Schema::dropIfExists('blog_comment');
    }
};
