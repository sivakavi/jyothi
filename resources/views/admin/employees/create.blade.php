@extends('admin.layouts.admin')

@section('title', 'Employee Create Form')

@section('content')

   <div class="page-header clearfix"></div>

    @include('error')

    <div class="row margin-top-30">
        <div class="col-md-8 center-margin">
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
                                <label for="name-field">Name</label>
                                <input type="text" id="name-field" name="name" class="form-control" value="{{ old("name") }}"/>
                                @if($errors->has("name"))
                                    <span class="help-block">{{ $errors->first("name") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('employee_id')) has-error @endif">
                                <label for="employee_id">Code</label>
                                <input type="text" id="employee_id" name="employee_id" class="form-control" value="{{ old("employee_id") }}"/>
                                @if($errors->has("employee_id"))
                                    <span class="help-block">{{ $errors->first("employee_id") }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="manager">Gender:</label>
                                <div class="">
                                    <input id="staff" type="radio" name="gender" value="male" checked> <label for="staff">Male</label>
                                    <input id="student" type="radio" name="gender" value="female"> <label for="student">Female</label>
                                </div>
                            </div>

                            <div class="form-group @if($errors->has('department_id')) has-error @endif">
                                <label for="name-field">Department</label>
                                <select class="form-control" name="department_id" id="department_id">
                                    <option value="">Select any one Department...</option>
                                    @foreach($departments as $department)
                                        <option value="{{$department->id}}">{{$department->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group @if($errors->has('category_id')) has-error @endif">
                                <label for="name-field">Category</label>
                                <select class="form-control" name="category_id" id="category_id">
                                    <option value="">Select any one Category...</option>
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group @if($errors->has('location_id')) has-error @endif">
                                <label for="name-field">Location</label>
                                <select class="form-control" name="location_id" id="category_id">
                                    <option value="">Select any one Location...</option>
                                    @foreach($locations as $location)
                                        <option value="{{$location->id}}">{{$location->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group @if($errors->has('cost_centre')) has-error @endif">
                                <label for="cost_centre">Cost Centre</label>
                                <input type="text" id="cost_centre" name="cost_centre" class="form-control" value="{{ old("cost_centre") }}"/>
                                @if($errors->has("cost_centre"))
                                    <span class="help-block">{{ $errors->first("cost_centre") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('cost_centre_desc')) has-error @endif">
                                <label for="cost_centre_desc">Cost Centre Description</label>
                                <input type="text" id="cost_centre_desc" name="cost_centre_desc" class="form-control" value="{{ old("cost_centre_desc") }}"/>
                                @if($errors->has("cost_centre_desc"))
                                    <span class="help-block">{{ $errors->first("cost_centre_desc") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('gl_accounts')) has-error @endif">
                                <label for="gl_accounts">GL Accounts</label>
                                <input type="text" id="gl_accounts" name="gl_accounts" class="form-control" value="{{ old("gl_accounts") }}"/>
                                @if($errors->has("gl_accounts"))
                                    <span class="help-block">{{ $errors->first("gl_accounts") }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has('gl_description')) has-error @endif">
                                <label for="gl_description">GL Description</label>
                                <input type="text" id="gl_description" name="gl_description" class="form-control" value="{{ old("gl_description") }}"/>
                                @if($errors->has("gl_description"))
                                    <span class="help-block">{{ $errors->first("gl_description") }}</span>
                                @endif
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
