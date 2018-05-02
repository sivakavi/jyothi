@extends('admin.layouts.admin')

@section('title', 'Employee Create Form')

@section('content')

   <div class="page-header clearfix"></div>

    @include('error')

    <div class="row margin-top-30">
        <div class="col-md-8 center-margin">
            <a href={{ asset('assets/demo/DemoEmployee.xlsx') }}><button class="btn btn-success" style="float:right;">Download Sample File</button></a>
            <br/>
            <br/>
            <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 30px;" action="{{ route('admin.employees.importExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="file" name="import_file" />
                @if($errors->has("import_file"))
                    <span class="help-block">{{ $errors->first("import_file") }}</span>
                @endif
                <br/>
                <button class="btn btn-primary">Import File</button>
            </form>
            <form class="form-horizontal form-label-left" action="{{ route('admin.employees.store') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Employee Create Form</h2>
                            <ul class="nav navbar-right">
                            <li class="cursor-pointer"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content margin-top-40">
                            <div class="form-group @if($errors->has('name')) has-error @endif">
                                <label for="name-field">Name*</label>
                                <input type="text" id="name-field" name="name" class="form-control" value="{{ old("name") }}"/>
                                @if($errors->has("name"))
                                    <span class="help-block">{{ $errors->first("name") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('employee_id')) has-error @endif">
                                <label for="employee_id">Code*</label>
                                <input type="text" id="employee_id" name="employee_id" class="form-control" value="{{ old("employee_id") }}"/>
                                @if($errors->has("employee_id"))
                                    <span class="help-block">{{ $errors->first("employee_id") }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="manager">Gender:*</label>
                                <div class="">
                                    <input id="staff" type="radio" name="gender" value="male" checked> <label for="staff">Male</label>
                                    <input id="student" type="radio" name="gender" value="female"> <label for="student">Female</label>
                                </div>
                            </div>

                            <div class="form-group @if($errors->has('department_id')) has-error @endif">
                                <label for="name-field">Department*</label>
                                <select class="form-control" name="department_id" id="department_id">
                                    <option value="">Select any one Department...</option>
                                    @foreach($departments as $department)
                                        <option value="{{$department->id}}">{{$department->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group @if($errors->has('category_id')) has-error @endif">
                                <label for="name-field">Category*</label>
                                <select class="form-control" name="category_id" id="category_id">
                                    <option value="">Select any one Category...</option>
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group @if($errors->has('location_id')) has-error @endif">
                                <label for="name-field">Location*</label>
                                <select class="form-control" name="location_id" id="category_id">
                                    <option value="">Select any one Location...</option>
                                    @foreach($locations as $location)
                                        <option value="{{$location->id}}">{{$location->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group @if($errors->has('cost_centre')) has-error @endif">
                                <label for="cost_centre">Cost Centre*</label>
                                <input type="text" id="cost_centre" name="cost_centre" class="form-control" value="{{ old("cost_centre") }}"/>
                                @if($errors->has("cost_centre"))
                                    <span class="help-block">{{ $errors->first("cost_centre") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('cost_centre_desc')) has-error @endif">
                                <label for="cost_centre_desc">Cost Centre Description*</label>
                                <input type="text" id="cost_centre_desc" name="cost_centre_desc" class="form-control" value="{{ old("cost_centre_desc") }}"/>
                                @if($errors->has("cost_centre_desc"))
                                    <span class="help-block">{{ $errors->first("cost_centre_desc") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('gl_accounts')) has-error @endif">
                                <label for="gl_accounts">GL Accounts*</label>
                                <input type="text" id="gl_accounts" name="gl_accounts" class="form-control" value="{{ old("gl_accounts") }}"/>
                                @if($errors->has("gl_accounts"))
                                    <span class="help-block">{{ $errors->first("gl_accounts") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('gl_description')) has-error @endif">
                                <label for="gl_description">GL Description*</label>
                                <input type="text" id="gl_description" name="gl_description" class="form-control" value="{{ old("gl_description") }}"/>
                                @if($errors->has("gl_description"))
                                    <span class="help-block">{{ $errors->first("gl_description") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('title')) has-error @endif">
                                <label for="title">Title</label>
                                <input type="text" id="title" name="title" class="form-control" value="{{ old("title") }}"/>
                                @if($errors->has("title"))
                                    <span class="help-block">{{ $errors->first("title") }}</span>
                                @endif
                            </div>
                            
                            <div class="form-group @if($errors->has('marital_status')) has-error @endif">
                                <label for="marital_status">Marital Status</label>
                                <input type="text" id="marital_status" name="marital_status" class="form-control" value="{{ old("marital_status") }}"/>
                                @if($errors->has("marital_status"))
                                    <span class="help-block">{{ $errors->first("marital_status") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('position_desc')) has-error @endif">
                                <label for="position_desc">Position Description</label>
                                <input type="text" id="position_desc" name="position_desc" class="form-control" value="{{ old("position_desc") }}"/>
                                @if($errors->has("position_desc"))
                                    <span class="help-block">{{ $errors->first("position_desc") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('perm_address')) has-error @endif">
                                <label for="perm_address">Permanent Address</label>
                                <input type="text" id="perm_address" name="perm_address" class="form-control" value="{{ old("perm_address") }}"/>
                                @if($errors->has("perm_address"))
                                    <span class="help-block">{{ $errors->first("perm_address") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('perm_city')) has-error @endif">
                                <label for="perm_city">Permanent City</label>
                                <input type="text" id="perm_city" name="perm_city" class="form-control" value="{{ old("perm_city") }}"/>
                                @if($errors->has("perm_city"))
                                    <span class="help-block">{{ $errors->first("perm_city") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('perm_district')) has-error @endif">
                                <label for="perm_district">Permanent District</label>
                                <input type="text" id="perm_district" name="perm_district" class="form-control" value="{{ old("perm_district") }}"/>
                                @if($errors->has("perm_district"))
                                    <span class="help-block">{{ $errors->first("perm_district") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('perm_state')) has-error @endif">
                                <label for="perm_state">Permanent State</label>
                                <input type="text" id="perm_state" name="perm_state" class="form-control" value="{{ old("perm_state") }}"/>
                                @if($errors->has("perm_state"))
                                    <span class="help-block">{{ $errors->first("perm_state") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('perm_country')) has-error @endif">
                                <label for="perm_country">Permanent Country</label>
                                <input type="text" id="perm_country" name="perm_country" class="form-control" value="{{ old("perm_country") }}"/>
                                @if($errors->has("perm_country"))
                                    <span class="help-block">{{ $errors->first("perm_country") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('perm_pincode')) has-error @endif">
                                <label for="perm_pincode">Permanent Pincode</label>
                                <input type="text" id="perm_pincode" name="perm_pincode" class="form-control" value="{{ old("perm_pincode") }}"/>
                                @if($errors->has("perm_pincode"))
                                    <span class="help-block">{{ $errors->first("perm_pincode") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('present_address')) has-error @endif">
                                <label for="present_address">Present Address</label>
                                <input type="text" id="present_address" name="present_address" class="form-control" value="{{ old("present_address") }}"/>
                                @if($errors->has("present_address"))
                                    <span class="help-block">{{ $errors->first("present_address") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('present_city')) has-error @endif">
                                <label for="present_city">Present City</label>
                                <input type="text" id="present_city" name="present_city" class="form-control" value="{{ old("present_city") }}"/>
                                @if($errors->has("present_city"))
                                    <span class="help-block">{{ $errors->first("present_city") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('present_district')) has-error @endif">
                                <label for="present_district">Present District</label>
                                <input type="text" id="present_district" name="present_district" class="form-control" value="{{ old("present_district") }}"/>
                                @if($errors->has("present_district"))
                                    <span class="help-block">{{ $errors->first("present_district") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('present_state')) has-error @endif">
                                <label for="present_state">Present State</label>
                                <input type="text" id="present_state" name="present_state" class="form-control" value="{{ old("present_state") }}"/>
                                @if($errors->has("present_state"))
                                    <span class="help-block">{{ $errors->first("present_state") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('present_country')) has-error @endif">
                                <label for="present_country">Present Country</label>
                                <input type="text" id="present_country" name="present_country" class="form-control" value="{{ old("present_country") }}"/>
                                @if($errors->has("present_country"))
                                    <span class="help-block">{{ $errors->first("present_country") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('present_pincode')) has-error @endif">
                                <label for="present_pincode">Present Pincode</label>
                                <input type="text" id="present_pincode" name="present_pincode" class="form-control" value="{{ old("present_pincode") }}"/>
                                @if($errors->has("present_pincode"))
                                    <span class="help-block">{{ $errors->first("present_pincode") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('official_email')) has-error @endif">
                                <label for="official_email">Official email</label>
                                <input type="text" id="official_email" name="official_email" class="form-control" value="{{ old("official_email") }}"/>
                                @if($errors->has("official_email"))
                                    <span class="help-block">{{ $errors->first("official_email") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('personal_mobile_no')) has-error @endif">
                                <label for="personal_mobile_no">Personal Mobile No</label>
                                <input type="text" id="personal_mobile_no" name="personal_mobile_no" class="form-control" value="{{ old("personal_mobile_no") }}"/>
                                @if($errors->has("personal_mobile_no"))
                                    <span class="help-block">{{ $errors->first("personal_mobile_no") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('personal_email_id')) has-error @endif">
                                <label for="personal_email_id">Personal email</label>
                                <input type="text" id="personal_email_id" name="personal_email_id" class="form-control" value="{{ old("personal_email_id") }}"/>
                                @if($errors->has("personal_email_id"))
                                    <span class="help-block">{{ $errors->first("personal_email_id") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('dob')) has-error @endif">
                                <label for="dob">Date Of Birth</label>
                                <input type="date" id="dob" name="dob" class="form-control" value="{{ old("dob") }}"/>
                                @if($errors->has("dob"))
                                    <span class="help-block">{{ $errors->first("dob") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('doj')) has-error @endif">
                                <label for="doj">Date Of Joining</label>
                                <input type="date" id="doj" name="doj" class="form-control" value="{{ old("doj") }}"/>
                                @if($errors->has("doj"))
                                    <span class="help-block">{{ $errors->first("doj") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('doc')) has-error @endif">
                                <label for="doc">Date Of Confirmation</label>
                                <input type="date" id="doc" name="doc" class="form-control" value="{{ old("doc") }}"/>
                                @if($errors->has("doc"))
                                    <span class="help-block">{{ $errors->first("doc") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('pan_no')) has-error @endif">
                                <label for="pan_no">PAN NO</label>
                                <input type="text" id="pan_no" name="pan_no" class="form-control" value="{{ old("pan_no") }}"/>
                                @if($errors->has("pan_no"))
                                    <span class="help-block">{{ $errors->first("pan_no") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('aadhar_no')) has-error @endif">
                                <label for="aadhar_no">Aadhar No</label>
                                <input type="text" id="aadhar_no" name="aadhar_no" class="form-control" value="{{ old("aadhar_no") }}"/>
                                @if($errors->has("aadhar_no"))
                                    <span class="help-block">{{ $errors->first("aadhar_no") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('pf_no')) has-error @endif">
                                <label for="pf_no">PF No</label>
                                <input type="text" id="pf_no" name="pf_no" class="form-control" value="{{ old("pf_no") }}"/>
                                @if($errors->has("pf_no"))
                                    <span class="help-block">{{ $errors->first("pf_no") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('uan_no')) has-error @endif">
                                <label for="uan_no">UAN No</label>
                                <input type="text" id="uan_no" name="uan_no" class="form-control" value="{{ old("uan_no") }}"/>
                                @if($errors->has("uan_no"))
                                    <span class="help-block">{{ $errors->first("uan_no") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('esic_no')) has-error @endif">
                                <label for="esic_no">ESIC No</label>
                                <input type="text" id="esic_no" name="esic_no" class="form-control" value="{{ old("esic_no") }}"/>
                                @if($errors->has("esic_no"))
                                    <span class="help-block">{{ $errors->first("esic_no") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('qualification')) has-error @endif">
                                <label for="qualification">Qualification</label>
                                <input type="text" id="qualification" name="qualification" class="form-control" value="{{ old("qualification") }}"/>
                                @if($errors->has("qualification"))
                                    <span class="help-block">{{ $errors->first("qualification") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('spouse_name')) has-error @endif">
                                <label for="spouse_name">Spouse Name</label>
                                <input type="text" id="spouse_name" name="spouse_name" class="form-control" value="{{ old("spouse_name") }}"/>
                                @if($errors->has("spouse_name"))
                                    <span class="help-block">{{ $errors->first("spouse_name") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('spouse_dob')) has-error @endif">
                                <label for="spouse_dob">Spouse DOB</label>
                                <input type="date" id="spouse_dob" name="spouse_dob" class="form-control" value="{{ old("spouse_dob") }}"/>
                                @if($errors->has("spouse_dob"))
                                    <span class="help-block">{{ $errors->first("spouse_dob") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('father_name')) has-error @endif">
                                <label for="father_name">Father Name</label>
                                <input type="text" id="father_name" name="father_name" class="form-control" value="{{ old("father_name") }}"/>
                                @if($errors->has("father_name"))
                                    <span class="help-block">{{ $errors->first("father_name") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('father_dob')) has-error @endif">
                                <label for="father_dob">Father DOB</label>
                                <input type="date" id="father_dob" name="father_dob" class="form-control" value="{{ old("father_dob") }}"/>
                                @if($errors->has("father_dob"))
                                    <span class="help-block">{{ $errors->first("father_dob") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('mother_name')) has-error @endif">
                                <label for="mother_name">Mother Name</label>
                                <input type="text" id="mother_name" name="mother_name" class="form-control" value="{{ old("mother_name") }}"/>
                                @if($errors->has("mother_name"))
                                    <span class="help-block">{{ $errors->first("mother_name") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('mother_dob')) has-error @endif">
                                <label for="mother_dob">Mother DOB</label>
                                <input type="date" id="mother_dob" name="mother_dob" class="form-control" value="{{ old("mother_dob") }}"/>
                                @if($errors->has("mother_dob"))
                                    <span class="help-block">{{ $errors->first("mother_dob") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('child1_name')) has-error @endif">
                                <label for="child1_name">Child-1 Name</label>
                                <input type="text" id="child1_name" name="child1_name" class="form-control" value="{{ old("child1_name") }}"/>
                                @if($errors->has("child1_name"))
                                    <span class="help-block">{{ $errors->first("child1_name") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('child1_dob')) has-error @endif">
                                <label for="child1_dob">Child-1 DOB</label>
                                <input type="date" id="child1_dob" name="child1_dob" class="form-control" value="{{ old("child1_dob") }}"/>
                                @if($errors->has("child1_dob"))
                                    <span class="help-block">{{ $errors->first("child1_dob") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('child2_name')) has-error @endif">
                                <label for="child2_name">Child-2 Name</label>
                                <input type="text" id="child2_name" name="child2_name" class="form-control" value="{{ old("child2_name") }}"/>
                                @if($errors->has("child2_name"))
                                    <span class="help-block">{{ $errors->first("child2_name") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('child2_dob')) has-error @endif">
                                <label for="child2_dob">Child-2 DOB</label>
                                <input type="date" id="child2_dob" name="child2_dob" class="form-control" value="{{ old("child2_dob") }}"/>
                                @if($errors->has("child2_dob"))
                                    <span class="help-block">{{ $errors->first("child2_dob") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('blood_group')) has-error @endif">
                                <label for="blood_group">Blood Group</label>
                                <input type="text" id="blood_group" name="blood_group" class="form-control" value="{{ old("blood_group") }}"/>
                                @if($errors->has("blood_group"))
                                    <span class="help-block">{{ $errors->first("blood_group") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('reporting_manager')) has-error @endif">
                                <label for="reporting_manager">Reporting Manager</label>
                                <input type="text" id="reporting_manager" name="reporting_manager" class="form-control" value="{{ old("reporting_manager") }}"/>
                                @if($errors->has("reporting_manager"))
                                    <span class="help-block">{{ $errors->first("reporting_manager") }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="manager">Remark</label>
                                <div class="">
                                    <input id="remark" type="radio" name="remark" value="active" checked <label for="remark">Active</label>
                                    <input id="remark" type="radio" name="remark" value="inactive" <label for="remark">InActive</label>
                                </div>
                            </div>

                            <div class="well well-sm margin-top-50">
                                <button type="submit" class="btn btn-primary btn-round btn-sm">Create Employee</button>
                                <a class="btn btn-link pull-right" href="{{ route('admin.departments.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
    
@endsection
