<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('package_name',50);
			$table->decimal('cost_per_month',10,2);
			$table->decimal('cost_per_year',10,2);
            $table->string('staff_limit');
            $table->string('contacts_limit');
            $table->string('invoice_limit');
            $table->string('quotation_limit');
            $table->string('recurring_transaction');
			$table->string('live_chat');
			$table->string('file_manager');
            $table->string('inventory_module')->nullable();
            $table->string('pos_module')->nullable();
			$table->string('hrm_module')->nullable();
			$table->string('payroll_module')->nullable();
			$table->string('project_management_module')->nullable();
			$table->string('online_payment');
			$table->tinyInteger('is_featured')->default(0);
			$table->text('others')->nullable();
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
        Schema::dropIfExists('packages');
    }
}
