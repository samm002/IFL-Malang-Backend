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
      Schema::create('donations', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('name')->default('anonim');
        $table->string('email');
        $table->tinyInteger('anonim')->default(0);
        $table->decimal('donation_amount', 12, 2);
        $table->text('donation_message')->nullable();
        $table->enum('status', ['unpaid', 'pending', 'paid', 'denied', 'expired', 'canceled']);

        $table->foreignUuid('campaign_id')
              ->constrained('campaigns')
              ->onDelete('cascade');

        $table->foreignUuid('user_id')
              ->constrained('users')
              ->onDelete('cascade')
              ->nullable();

        $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donations');
    }
};
