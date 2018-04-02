@extends('admin.layouts.admin')

@section('title', 'Employee Detail')

@section('content')
<div class="page-header clearfix"></div>
    <div class="row margin-top-30">
        <div class="col-md-8 col-sm-12 col-xs-12 center-margin">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>{{$employee->employee_id}} <small>Details</small></h2>
                        <ul class="nav navbar-right">
                            <li class="cursor-pointer"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content margin-top-30">

                        <table class="table table-bordered">
                        <tbody>
                            <tr>
                            <th scope="row">Code</th>
                            <td>{{$employee->id}}</td>
                            </tr>
                            <tr>
                            <th scope="row">NAME</th>
                            <td>{{$employee->name}}</td>
                            </tr>
                            <tr>
                            <th scope="row">GENDER</th>
                            <td>{{$employee->gender}}</td>
                            </tr>
                            <tr>
                            <th scope="row">DEPARTMENT</th>
                            <td>{{$employee->department->name}}</td>
                            </tr>
                            <tr>
                            <th scope="row">CATEGORY</th>
                            <td>{{$employee->category->name}}</td>
                            </tr>
                            <tr>
                            <th scope="row">LOCATION</th>
                            <td>{{$employee->location->name}}</td>
                            </tr>
                            <tr>
                            <th scope="row">COST CENTRE</th>
                            <td>{{$employee->cost_centre}}</td>
                            </tr>
                            <tr>
                            <th scope="row">COST CENTRE DESC</th>
                            <td>{{$employee->cost_centre_desc}}</td>
                            </tr>
                            <tr>
                            <th scope="row">GL ACCOUNT</th>
                            <td>{{$employee->gl_accounts}}</td>
                            </tr>
                            <tr>
                            <th scope="row">GL ACCOUNT DESC</th>
                            <td>{{$employee->gl_description}}</td>
                            </tr>
                            <tr>
                        </tbody>
                        </table>
                    </div>
                </div>
        </div>
    </div>

    <a class="btn btn-link" href="{{ route('admin.employees.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
@endsection