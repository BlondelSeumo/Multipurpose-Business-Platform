<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('quotation_number');
            $table->date('quotation_date');
            $table->string('template',100)->nullable();
            $table->decimal('grand_total',10,2);
            $table->decimal('converted_total',10,2)->nullable();
            $table->decimal('tax_total',10,2);
            $table->text('note')->nullable();
			$table->string('related_to',20)->nullable();
			$table->bigInteger('related_id')->nullable();
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
        Schema::dropIfExists('quotations');
    }
}
