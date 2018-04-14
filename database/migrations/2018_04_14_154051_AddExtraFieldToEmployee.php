<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldToEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function($table) {            
            $table->string('title')->nullable()->before('created_at');
            $table->string('marital_status')->nullable()->before('created_at');
            $table->string('position_desc')->nullable()->before('created_at');
            $table->string('perm_address')->nullable()->before('created_at');
            $table->string('perm_city')->nullable()->before('created_at');
            $table->string('perm_district')->nullable()->before('created_at');
            $table->string('perm_state')->nullable()->before('created_at');
            $table->string('perm_country')->nullable()->before('created_at');
            $table->string('perm_pincode')->nullable()->before('created_at');
            $table->string('present_address')->nullable()->before('created_at');
            $table->string('present_city')->nullable()->before('created_at');
            $table->string('present_district')->nullable()->before('created_at');
            $table->string('present_state')->nullable()->before('created_at');
            $table->string('present_country')->nullable()->before('created_at');
            $table->string('present_pincode')->nullable()->before('created_at');
            $table->string('official_email')->nullable()->before('created_at');
            $table->string('personal_mobile_no')->nullable()->before('created_at');
            $table->string('personal_email_id')->nullable()->before('created_at');
            $table->date('dob')->nullable()->before('created_at');
            $table->date('doj')->nullable()->before('created_at');
            $table->date('doc')->nullable()->before('created_at');
            $table->string('pan_no')->nullable()->before('created_at');
            $table->string('aadhar_no')->nullable()->before('created_at');
            $table->string('pf_no')->nullable()->before('created_at');
            $table->string('uan_no')->nullable()->before('created_at');
            $table->string('esic_no')->nullable()->before('created_at');
            $table->string('qualification')->nullable()->before('created_at');
            $table->string('spouse_name')->nullable()->before('created_at');
            $table->date('spouse_dob')->nullable()->before('created_at');
            $table->string('father_name')->nullable()->before('created_at');
            $table->date('father_dob')->nullable()->before('created_at');
            $table->string('mother_name')->nullable()->before('created_at');
            $table->date('mother_dob')->nullable()->before('created_at');
            $table->string('child1_name')->nullable()->before('created_at');
            $table->date('child1_dob')->nullable()->before('created_at');
            $table->string('child2_name')->nullable()->before('created_at');
            $table->date('child2_dob')->nullable()->before('created_at');
            $table->string('blood_group')->nullable()->before('created_at');
            $table->string('reporting_manager')->nullable()->before('created_at');
            $table->string('remark')->nullable()->before('created_at');
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
        Schema::table('employees', function($table) {
            $table->dropColumn('title');
            $table->dropColumn('marital_status');
            $table->dropColumn('position_desc');
            $table->dropColumn('perm_address');
            $table->dropColumn('perm_city');
            $table->dropColumn('perm_district');
            $table->dropColumn('perm_state');
            $table->dropColumn('perm_country');
            $table->dropColumn('perm_pincode');
            $table->dropColumn('present_address');
            $table->dropColumn('present_city');
            $table->dropColumn('present_district');
            $table->dropColumn('present_state');
            $table->dropColumn('present_country');
            $table->dropColumn('present_pincode');
            $table->dropColumn('official_email');
            $table->dropColumn('personal_mobile_no');
            $table->dropColumn('personal_email_id');
            $table->dropColumn('dob');
            $table->dropColumn('doj');
            $table->dropColumn('doc');
            $table->dropColumn('pan_no');
            $table->dropColumn('aadhar_no');
            $table->dropColumn('pf_no');
            $table->dropColumn('uan_no');
            $table->dropColumn('esic_no');
            $table->dropColumn('qualification');
            $table->dropColumn('spouse_name');
            $table->dropColumn('spouse_dob');
            $table->dropColumn('father_name');
            $table->dropColumn('father_dob');
            $table->dropColumn('mother_name');
            $table->dropColumn('mother_dob');
            $table->dropColumn('child1_name');
            $table->dropColumn('child1_dob');
            $table->dropColumn('child2_name');
            $table->dropColumn('child2_dob');
            $table->dropColumn('blood_group');
            $table->dropColumn('reporting_manager');
            $table->dropColumn('remark');
        });
    }
}
