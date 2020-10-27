<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('name',50);
			$table->string('company_name',50)->nullable();
			$table->string('email')->nullable();
            $table->integer('converted_lead')->nullable();
			$table->bigInteger('lead_status_id');
			$table->bigInteger('lead_source_id');
			$table->bigInteger('assigned_user_id');
			$table->bigInteger('created_user_id');
			$table->date('contact_date');
			$table->string('phone',20)->nullable();
			$table->string('website')->nullable();
			$table->string('country',50)->nullable();
			$table->string('currency',3);
			$table->string('vat_id')->nullable();
            $table->string('reg_no')->nullable();
			$table->string('city',50)->nullable();
			$table->string('state',50)->nullable();
			$table->string('zip',20)->nullable();
			$table->text('address')->nullable();
            $table->longText('custom_fields')->nullable();
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
        Schema::dropIfExists('leads');
    }
}
