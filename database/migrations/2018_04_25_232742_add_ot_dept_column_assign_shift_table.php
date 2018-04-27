<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOtDeptColumnAssignShiftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_shifts', function($table) {
            $table->integer('ot_department_id')->unsigned();
            $table->foreign('ot_department_id', 'foreign_user_ot_department_id')
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
            $table->dropForeign('foreign_user_ot_department_id');
            $table->dropColumn('ot_department_id');
        });
    }
}
