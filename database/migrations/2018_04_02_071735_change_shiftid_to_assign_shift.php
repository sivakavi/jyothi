<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeShiftidToAssignShift extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('assign_shifts', function($table) {
            $table->integer('changed_shift_id')->unsigned();
            $table->foreign('changed_shift_id', 'foreign_user_changed_shift_id')
                ->references('id')
                ->on('shifts')
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
        //
        Schema::table('assign_shifts', function($table) {
            $table->dropColumn('changed_shift_id');
        });
    }
}
