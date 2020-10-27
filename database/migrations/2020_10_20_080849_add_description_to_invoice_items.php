<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionToInvoiceItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->text('description')->after('item_id')->nullable();
        });
		
		Schema::table('quotation_items', function (Blueprint $table) {
            $table->text('description')->after('item_id')->nullable();
        });
		
		Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->text('description')->after('product_id')->nullable();
        });
		
		Schema::table('purchase_return_items', function (Blueprint $table) {
            $table->text('description')->after('product_id')->nullable();
        });

        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->text('description')->after('product_id')->nullable();
        });
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
		
		Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
		
		Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
		
		Schema::table('purchase_return_items', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });

        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
    }
}
