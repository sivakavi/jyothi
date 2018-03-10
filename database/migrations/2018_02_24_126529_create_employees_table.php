<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('gender');
            $table->integer('department_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->string('location');
            $table->string('cost_centre');
            $table->string('cost_centre_desc');
            $table->bigInteger('gl_accounts');
            $table->bigInteger('gl_description');
            $table->foreign('department_id', 'foreign_user')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
            $table->foreign('category_id', 'foreign_user_category')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
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
        Schema::dropIfExists('employees');
    }
}
