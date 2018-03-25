<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAssignShiftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_shifts', function($table) {
            $table->integer('changed_department_id')->unsigned();
            $table->foreign('changed_department_id', 'foreign_user_change_department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assign_shifts', function($table) {
            $table->dropColumn('changed_department_id');
        });
    }
}
