<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_manager', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',64);
            $table->string('mime_type')->nullable();
            $table->string('is_dir',3)->default('no');
            $table->string('file')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->bigInteger('company_id');
            $table->bigInteger('created_by');
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
        Schema::dropIfExists('file_manager');
    }
}
