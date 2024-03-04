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
        $table->decimal('donation_amount', 12, 2);
        $table->text('donation_message')->nullable();
        $table->timestamp('donation_date')->nullable();
        $table->enum('status', ['unpaid', 'pending', 'paid', 'denied', 'expired', 'canceled']);
        $table->string('payment_method')->nullable();
        $table->uuid('campaign_id');
        $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
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
