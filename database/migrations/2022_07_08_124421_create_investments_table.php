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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->nullable();
            $table->string('investment_type')->nullable();
            $table->string('investment_title')->nullable();
            $table->string('frequency')->nullable();
            $table->string('investment_date')->nullable();
            $table->string('investment_amount')->nullable();
            $table->string('returns')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('card_no')->nullable();
            $table->string('card_expiry_date')->nullable();
            $table->string('card_cvv')->nullable();
            $table->string('balance')->nullable();
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
        Schema::dropIfExists('investments');
    }
};
