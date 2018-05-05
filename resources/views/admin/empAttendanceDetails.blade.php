@extends('admin.layouts.admin')

@section('title', "$departmentName Department ($departmentCode) Employee Attendance Details List - $today")

@section('content')
    <div class="page-header clearfix">
    </div>
    <div class="row" style="margin-top:80px;">
        <div class="col-md-12">
        <h3>Absent Details</h3>
            @if($absentEmployees->count())
                <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%">
                    <thead>
                        <tr>
                        <th>ID</th>
                        <th>CODE</th>
                        <th>NAME</th>
                        <th>GENDER</th>
                        <th>DEPARTMENT</th>
                        <th>CATEGORY</th>
                        <th>LOCATION</th>
                        <th>REMARK</th>
                        <!-- <th>COST CENTRE</th>
                        <th>COST CENTRE DESCRIPTION</th>
                        <th>GL ACCOUNTS</th>
                        <th>GL DESCRIPTION</th> -->
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($absentEmployees as $employee)
                            <tr>
                                <td>{{$employee->id}}</td>
                                <td>{{$employee->employee_id}}</td>
                                <td>{{$employee->name}}</td>
                                <td>{{ucfirst($employee->gender)}}</td>
                                <td>{{$employee->department->name}}</td>
                                <td>{{$employee->category->name}}</td>
                                <td>{{$employee->location->name}}</td>
                                <td>{{ucfirst($employee->remark)}}</td>
                                <!-- <td>{{$employee->cost_centre}}</td>
                                <td>{{$employee->cost_centre_desc}}</td>
                                <td>{{$employee->gl_accounts}}</td>
                                <td>{{$employee->gl_description}}</td> -->
                                <td class="text-right">
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.employees.show', $employee->id) }}"><i class="glyphicon glyphicon-eye-open"></i> View</a>
                                    <a class="btn btn-xs btn-warning" href="{{ route('admin.employees.edit', $employee->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif

        </div>
        <div class="col-md-12">
            <h3>Present Details</h3>
            @if($presentEmployees->count())
                <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%">
                    <thead>
                        <tr>
                        <th>ID</th>
                        <th>CODE</th>
                        <th>NAME</th>
                        <th>GENDER</th>
                        <th>DEPARTMENT</th>
                        <th>CATEGORY</th>
                        <th>LOCATION</th>
                        <th>REMARK</th>
                        <!-- <th>COST CENTRE</th>
                        <th>COST CENTRE DESCRIPTION</th>
                        <th>GL ACCOUNTS</th>
                        <th>GL DESCRIPTION</th> -->
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($presentEmployees as $employee)
                            <tr>
                                <td>{{$employee->id}}</td>
                                <td>{{$employee->employee_id}}</td>
                                <td>{{$employee->name}}</td>
                                <td>{{ucfirst($employee->gender)}}</td>
                                <td>{{$employee->department->name}}</td>
                                <td>{{$employee->category->name}}</td>
                                <td>{{$employee->location->name}}</td>
                                <td>{{ucfirst($employee->remark)}}</td>
                                <!-- <td>{{$employee->cost_centre}}</td>
                                <td>{{$employee->cost_centre_desc}}</td>
                                <td>{{$employee->gl_accounts}}</td>
                                <td>{{$employee->gl_description}}</td> -->
                                <td class="text-right">
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.employees.show', $employee->id) }}"><i class="glyphicon glyphicon-eye-open"></i> View</a>
                                    <a class="btn btn-xs btn-warning" href="{{ route('admin.employees.edit', $employee->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif

        </div>
    </div>

@endsection