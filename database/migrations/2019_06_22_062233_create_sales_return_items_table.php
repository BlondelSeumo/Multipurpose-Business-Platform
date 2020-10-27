<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesReturnItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sales_return_id');
            $table->bigInteger('product_id');
            $table->decimal('quantity',8,2);
            $table->decimal('unit_cost',10,2);
            $table->decimal('discount',10,2);
			$table->string('tax_method',10)->nullable();
            $table->bigInteger('tax_id')->nullable();
            $table->decimal('tax_amount',10,2)->nullable();
            $table->decimal('sub_total',10,2);
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
        Schema::dropIfExists('sales_return_items');
    }
}
