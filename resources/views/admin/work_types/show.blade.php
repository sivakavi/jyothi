@extends('admin.layouts.admin')

@section('title', 'Process Show')

@section('content')
<div class="page-header clearfix"></div>
    <div class="row margin-top-30">
        <div class="col-md-8 col-sm-12 col-xs-12 center-margin">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>{{$work_type->name}} <small>Details</small></h2>
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
                            <th scope="row">ID</th>
                            <td>{{$work_type->id}}</td>
                            </tr>
                            <tr>
                            <th scope="row">NAME</th>
                            <td>{{$work_type->name}}</td>
                            </tr>
                            <tr>
                            <th scope="row">DEPARTMENT NAME</th>
                            <td>{{$work_type->department->name}}</td>
                            </tr>
                            <tr>
                        </tbody>
                        </table>
                    </div>
                </div>
        </div>
    </div>

    <a class="btn btn-link" href="{{ route('admin.work_types.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
@endsection