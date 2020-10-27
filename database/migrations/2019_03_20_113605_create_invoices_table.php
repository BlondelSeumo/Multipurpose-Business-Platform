<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('grand_total',10,2);
            $table->decimal('tax_total',10,2);
            $table->decimal('paid',10,2)->nullable();
            $table->decimal('converted_total',10,2)->nullable();
            $table->string('status',20);
            $table->string('template',100)->nullable();
            $table->text('note')->nullable();
			$table->string('related_to',20)->nullable();
			$table->bigInteger('related_id')->nullable();
			$table->bigInteger('client_id');
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
        Schema::dropIfExists('invoices');
    }
}
