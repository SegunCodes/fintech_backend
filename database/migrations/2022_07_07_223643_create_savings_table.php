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
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->nullable();
            $table->string('savings_type')->nullable();
            $table->string('savings_name')->nullable();
            $table->string('frequency')->nullable();
            $table->string('members_limit')->nullable();
            $table->string('safe_amount')->nullable();
            $table->string('rotating_amount')->nullable();
            $table->string('target_amount')->nullable();
            $table->string('monthly_deposit')->nullable();
            $table->string('debit_day')->nullable();
            $table->string('start_date')->nullable();
            $table->string('stop_date')->nullable();
            $table->string('withdrawal_type')->nullable();
            $table->string('withdrawal_account_name')->nullable();
            $table->string('withdrawal_account_number')->nullable();
            $table->string('position')->nullable();
            $table->string('invite_role')->nullable();
            $table->string('fund_collector')->nullable();
            $table->string('member_limit')->nullable();
            $table->string('card_no')->nullable();
            $table->string('card_expiry_date')->nullable();
            $table->string('card_cvv')->nullable();
            $table->string('payment_method')->nullable();
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
        Schema::dropIfExists('savings');
    }
};
