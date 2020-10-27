<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('profile_type',20);
			$table->string('company_name',50)->nullable();
			$table->string('contact_name',50);
			$table->string('contact_email',100);
			$table->string('vat_id')->nullable();
            $table->string('reg_no')->nullable();
			$table->string('contact_phone',20)->nullable();
			$table->string('country',50)->nullable();
			$table->string('currency',3);
			$table->string('city',50)->nullable();
			$table->string('state',50)->nullable();
			$table->string('zip',20)->nullable();
			$table->text('address')->nullable();
			$table->string('facebook')->nullable();
			$table->string('twitter')->nullable();
			$table->string('linkedin')->nullable();
			$table->text('remarks')->nullable();
			$table->string('contact_image')->nullable();
            $table->bigInteger('group_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('company_id');
			$table->longText('custom_fields')->nullable();
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
        Schema::dropIfExists('contacts');
    }
}
