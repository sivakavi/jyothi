@extends('admin.layouts.admin')

@section('title', __('views.admin.dashboard.title'))

@section('content')
    <div class="page-header clearfix"></div>
    <div style="margin-top:80px;">
        <div class="row">
            <div class="animated flipInY col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <a href="{{ route('admin.departments.index') }}">
                    <div class="tile-stats" style="padding:20px 0px !important;">
                        <div class="icon" style="top:45px !important;right:80px !important;"><i class="fa fa-institution" style="font-size:80px !important;"></i></div>
                        <div class="count">{{$departments}}</div>
                        <h3>Departments</h3>
                    </div>
                </a>
            </div>
            <div class="animated flipInY col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <a href="{{ route('admin.shifts.index') }}">
                    <div class="tile-stats" style="padding:20px 0px !important;">
                        <div class="icon" style="top:45px !important;right:80px !important;"><i class="fa fa-cubes" style="font-size:80px !important;"></i></div>
                        <div class="count">{{$shifts}}</div>
                        <h3>Shifts</h3>
                    </div>
                </a>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="animated flipInY col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <a href="{{ route('admin.users') }}">
                    <div class="tile-stats" style="padding:20px 0px !important;">
                        <div class="icon" style="top:45px !important;right:80px !important;"><i class="fa fa-user" style="font-size:80px !important;"></i></div>
                        <div class="count">{{$users}}</div>
                        <h3>Users</h3>
                    </div>
                </a>
            </div>
            <div class="animated flipInY col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <a href="{{ route('admin.employees.index') }}">
                    <div class="tile-stats" style="padding:20px 0px !important;">
                        <div class="icon" style="top:45px !important;right:80px !important;"><i class="fa fa-child" style="font-size:80px !important;"></i></div>
                        <div class="count">{{$employees}}</div>
                        <h3>Employees</h3>
                    </div>
                </a>
            </div>
            <br>
            <h3>Attendance Department Details for {{$today}}</h3>
            <div class="animated flipInY col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="tile-stats" style="padding:20px 0px !important;">
                    <div class="icon" style="top:45px !important;right:80px !important;"><i class="fa fa-institution" style="font-size:80px !important;"></i></div>
                    <div style="margin-left: 25px;">
                    <h4>Whole Department Details</h4>
                    <h5>Present - {{ isset($wholeDatas['present']) ? $wholeDatas['present']:'0'}}</h6>
                    <h5 style="margin-bottom: 25px;">Absent - {{ isset($wholeDatas['absent']) ? $wholeDatas['absent']:'0'}}</h6>
                    <h6 style="font-weight: 900;">Process</h6>
                    @foreach ($wholeDatas as $key => $value)
                        @if($key != 'name' && $key != 'present' && $key != 'absent') 
                            <h6>{{$key}} - {{$value}}</h6>
                        @endif
                    @endforeach
                    </div>
                </div>
            </div>
            @foreach ($departmentDatas as $departmentData)
                <a href="{{ URL::route('admin.getDepartmentEmployeeAttendance', array('department_id'=> $departmentData['department_id'])) }}">
                    <div class="animated flipInY col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="tile-stats" style="padding:20px 0px !important;">
                            <div class="icon" style="top:45px !important;right:80px !important;"><i class="fa fa-institution" style="font-size:80px !important;"></i></div>
                            <div style="margin-left: 25px;">
                            <h4>{{ $departmentData['name']}} Department Details</h4>
                            <h5>Present - {{ isset($departmentData['present']) ? $departmentData['present']:'0'}}</h6>
                            <h5 style="margin-bottom: 25px;">Absent - {{ isset($departmentData['absent']) ? $departmentData['absent']:'0'}}</h6>
                            <h6 style="font-weight: 900;">Process</h6>
                            @foreach ($departmentData as $key => $value)
                                @if($key != 'name' && $key != 'present' && $key != 'absent') 
                                    <h6>{{$key}} - {{$value}}</h6>
                                @endif
                            @endforeach
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection