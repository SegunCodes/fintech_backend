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
            $table->id();
            $table->string('user_name')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('service_provider')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('service')->nullable();
            $table->string('phone')->nullable();           
            $table->string('data')->nullable();
            $table->string('unit')->nullable();
            $table->string('amount')->nullable();
            $table->string('account')->nullable();
            $table->string('destination_acct_name')->nullable();
            $table->string('destination_acct_no')->nullable();
            $table->string('card_no')->nullable();
            $table->string('card_expiry_date')->nullable();
            $table->string('card_cvv')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('txn_reference')->nullable();
            $table->string('txn_charges')->nullable();
            $table->string('status')->nullable();
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
