<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('order_date');
            $table->bigInteger('supplier_id');
            $table->tinyInteger('order_status');
            $table->bigInteger('order_tax_id')->nullable();
            $table->decimal('order_tax',10,2)->nullable();
            $table->decimal('order_discount',10,2);
            $table->decimal('shipping_cost',10,2);
            $table->decimal('product_total',10,2);
            $table->decimal('grand_total',10,2);
            $table->decimal('paid',10,2);
            $table->tinyInteger('payment_status');
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
        Schema::dropIfExists('purchase_orders');
    }
}
