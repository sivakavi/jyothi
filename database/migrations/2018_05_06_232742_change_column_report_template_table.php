<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnReportTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_templates', function($table) {
            $table->string('frontend_data', 250)->change();
            $table->string('backend_data', 250)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_templates', function($table) {
            $table->string('frontend_data', 191)->change();
            $table->string('backend_data', 191)->change();
        });
    }
}
