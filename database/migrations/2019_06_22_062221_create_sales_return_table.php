<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesReturnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_return', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('return_date');
            $table->bigInteger('customer_id');
            $table->bigInteger('tax_id')->nullable();
            $table->decimal('tax_amount',10,2)->nullable();
            $table->decimal('product_total',10,2);
            $table->decimal('grand_total',10,2);
            $table->decimal('converted_total',10,2)->nullable();
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
        Schema::dropIfExists('sales_return');
    }
}
