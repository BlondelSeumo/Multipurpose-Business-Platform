<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('title');
			$table->text('description')->nullable();
			$table->bigInteger('project_id');
			$table->bigInteger('milestone_id')->nullable();
			$table->string('priority',15);
			$table->bigInteger('task_status_id');
			$table->bigInteger('assigned_user_id')->nullable();
			$table->date('start_date');
			$table->date('end_date')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
