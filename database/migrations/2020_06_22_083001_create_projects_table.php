<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('name');
			$table->bigInteger('client_id');
			$table->integer('progress')->nullable();
			$table->string('billing_type',20);
			$table->string('status',20);
			$table->decimal('fixed_rate',10,2)->nullable();
			$table->decimal('hourly_rate',10,2)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->longText('custom_fields')->nullable();
			$table->bigInteger('user_id');
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
        Schema::dropIfExists('projects');
    }
}
