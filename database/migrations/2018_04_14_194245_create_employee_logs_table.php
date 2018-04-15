<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->unsigned();
            $table->integer('department_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('location_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('cost_centre');
            $table->string('gl_accounts');
            $table->string('action');
            $table->foreign('employee_id', 'foreign_user_employee_id99')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
            $table->foreign('department_id', 'foreign_employee_department_id99')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
            $table->foreign('location_id', 'foreign_employee_location99')
                ->references('id')
                ->on('locations')
                ->onDelete('cascade');
            $table->foreign('category_id', 'foreign_employee_category99')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
            $table->foreign('user_id', 'foreign_user99')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('employee_logs');
    }
}
