<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseReturnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_return', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('return_date');
            $table->bigInteger('supplier_id')->nullable();
            $table->bigInteger('account_id');
            $table->bigInteger('chart_id');
            $table->bigInteger('payment_method_id');
            $table->bigInteger('tax_id')->nullable();
            $table->decimal('tax_amount',10,2)->nullable();
            $table->decimal('product_total',10,2);
            $table->decimal('grand_total',10,2);
            $table->text('attachemnt')->nullable();
            $table->text('note')->nullable();
            $table->bigInteger('company_id');
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
        Schema::dropIfExists('purchase_return');
    }
}
