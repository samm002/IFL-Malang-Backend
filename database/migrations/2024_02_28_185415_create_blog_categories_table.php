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
        Schema::create('blog_categories', function (Blueprint $table) {
          // $table->uuid('id')->primary();
          $table->id();
          $table->uuid('blog_id')->unique();
          $table->uuid('categories_id');
          $table->timestamps();
  
          $table->foreign('blog_id')
            ->references('id')
            ->on('blog')
            ->onDelete('cascade');
  
          $table->foreign('categories_id')
            ->references('id')
            ->on('categories')
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
        Schema::dropIfExists('blog_categories');
    }
};
