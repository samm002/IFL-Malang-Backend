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
      Schema::create('campaigns', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('name')->unique();
        $table->enum('type', ['kemanusiaan', 'kesehatan', 'pendidikan', 'tanggap bencana']);
        $table->decimal('current_donation', 12, 2)->default(0);
        $table->decimal('target_donation', 12, 2);
        $table->date('start_date');
        $table->date('end_date');
        $table->text('description')->nullable();
        $table->string('photo')->nullable();
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
        Schema::dropIfExists('campaigns');
    }
};
