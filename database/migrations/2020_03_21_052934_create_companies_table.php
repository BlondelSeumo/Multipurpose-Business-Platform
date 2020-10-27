<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('business_name');
            $table->integer('status')->unsigned();
            $table->integer('package_id')->nullable();
            $table->string('package_type',10)->nullable();
			$table->string('membership_type',10)->nullable();
			$table->date('valid_to');
			$table->date('last_email')->nullable();
			$table->string('staff_limit',20)->nullable();
            $table->string('contacts_limit',20)->nullable();
            $table->string('invoice_limit',20)->nullable();
            $table->string('quotation_limit',20)->nullable();
            $table->string('recurring_transaction',3)->nullable();
			$table->string('live_chat',3)->nullable();
			$table->string('file_manager',3)->nullable();
			$table->string('inventory_module',3)->nullable();
			$table->string('pos_module',3)->nullable();
			$table->string('hrm_module',3)->nullable();
			$table->string('payroll_module',3)->nullable();
			$table->string('project_management_module',3)->nullable();
			$table->string('online_payment',3)->nullable();
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
        Schema::dropIfExists('companies');
    }
}
