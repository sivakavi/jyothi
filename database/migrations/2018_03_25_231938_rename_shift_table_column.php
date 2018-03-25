<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameShiftTableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shifts', function($table) {
            $table->renameColumn('in', 'intime');
            $table->renameColumn('out', 'outtime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shifts', function($table) {
            $table->renameColumn('intime', 'in');
            $table->renameColumn('outtime', 'out');
        });
    }
}
