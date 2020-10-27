<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('title');
			$table->text('description')->nullable();
			$table->date('due_date');
			$table->string('status',20);
			$table->decimal('cost',10,2)->nullable();
			$table->bigInteger('project_id');
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
        Schema::dropIfExists('project_milestones');
    }
}
