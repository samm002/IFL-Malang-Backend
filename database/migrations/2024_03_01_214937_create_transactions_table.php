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
      Schema::create('transactions', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('snap_token')->nullable();
        $table->string('midtrans_transaction_id')->nullable();
        $table->enum('status', ['unpaid', 'pending', 'paid', 'denied', 'expired', 'canceled'])->nullable();
        $table->string('payment_method')->nullable();
        $table->string('payment_provider')->nullable();
        $table->string('bank')->nullable();
        $table->string('va_number')->nullable();
        $table->timestamp('transaction_success_time')->nullable();
        $table->uuid('donation_id');
        $table->foreign('donation_id')->references('id')->on('donations')->onDelete('cascade');
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
        Schema::dropIfExists('transactions');
    }
};
