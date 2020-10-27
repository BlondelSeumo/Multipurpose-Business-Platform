<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepeatingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repeating_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('trans_date');
            $table->bigInteger('account_id');
            $table->bigInteger('chart_id');
            $table->string('type',10);
            $table->string('dr_cr',2);
            $table->decimal('amount',10,2);
            $table->decimal('base_amount',10,2)->nullable();
            $table->bigInteger('payer_payee_id')->nullable();
            $table->bigInteger('payment_method_id');
            $table->string('reference',50)->nullable();
            $table->text('note')->nullable();
            $table->bigInteger('company_id');
			$table->tinyInteger('status')->nullable()->default(0);
			$table->bigInteger('trans_id')->nullable();
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
        Schema::dropIfExists('repeating_transactions');
    }
}
