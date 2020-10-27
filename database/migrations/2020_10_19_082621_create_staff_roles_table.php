<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('name',50);
			$table->text('description')->nullable();
			$table->bigInteger('company_id');
            $table->timestamps();
        });
	
		
		Schema::table('permissions', function (Blueprint $table) {
			$table->dropColumn('user_id');
            $table->bigInteger('role_id')->after('id');
        });
		
		Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('role_id')->after('user_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_roles');
		Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['role_id']);
			$table->bigInteger('user_id')->after('id');
        });
		Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role_id']);
        });
    }
	
}
