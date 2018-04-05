<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_shifts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('department_id')->unsigned();
            $table->integer('batch_id')->unsigned()->nullable();
            $table->integer('employee_id')->unsigned();
            $table->integer('shift_id')->unsigned();
            $table->integer('work_type_id')->unsigned();
            $table->integer('status_id')->unsigned()->nullable();
            $table->integer('leave_id')->unsigned()->nullable();
            $table->float('otHours')->nullable();
            $table->date('nowdate');
            $table->foreign('department_id', 'foreign_user_department_id6')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
            $table->foreign('batch_id', 'foreign_user_batch')
                ->references('id')
                ->on('batches')
                ->onDelete('cascade');
            $table->foreign('employee_id', 'foreign_user_employee')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
            $table->foreign('shift_id', 'foreign_user_shift')
                ->references('id')
                ->on('shifts')
                ->onDelete('cascade');
            $table->foreign('status_id', 'foreign_user_status')
                ->references('id')
                ->on('statuses')
                ->onDelete('cascade');
            $table->foreign('leave_id', 'foreign_user_leave')
                ->references('id')
                ->on('leaves')
                ->onDelete('cascade');
            $table->foreign('work_type_id', 'foreign_user_work_type')
                ->references('id')
                ->on('work_types')
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
        Schema::dropIfExists('assign_shifts');
    }
}
