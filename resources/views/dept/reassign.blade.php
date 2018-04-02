@extends('dept.layouts.dept')

@section('title', ' ReAssign Shift')

@section('content')
    <div class="page-header clearfix"></div>
    <div class="row margin-top-30">
        <div class="col-md-8 center-margin">
            <form class="form-horizontal form-label-left" action="{{ route('dept.employeeReassignStore') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="batch_id" value="{{ $_GET['batch_id'] }}">
            <input type="hidden" id="oToDate" name="toDate" value="{{ $batches['toDate'] }}">

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Re-Assign Form</h2>
                                    <ul class="nav navbar-right">
                                    <li class="cursor-pointer"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content margin-top-40">

                                    <div class="form-group @if($errors->has('name')) has-error @endif">
                                        <label for="name-field">Name</label>
                                        <input type="text" id="employee_name" name="employee_name" class="form-control" value="{{$batches['employee_name']}}" disabled/>
                                        @if($errors->has("name"))
                                            <span class="help-block">{{ $errors->first("name") }}</span>
                                        @endif
                                    </div>

                                    <div class="form-group @if($errors->has('name')) has-error @endif">
                                        <label for="name-field">From Date</label>
                                        <input type="date" id="fromDate" name="fromDate" class="form-control" value="@if (!$batches['check']) {{$batches['fromDate']}} @endif" required/>
                                        @if($errors->has("name"))
                                            <span class="help-block">{{ $errors->first("name") }}</span>
                                        @endif
                                    </div>

                                    <div class="form-group @if($errors->has('name')) has-error @endif">
                                        <label for="name-field">To Date</label>
                                        <input type="date" id="toDate" class="form-control" value="{{$batches['toDate']}}" @if($batches['check']) disabled @endif/>
                                        @if($errors->has("name"))
                                            <span class="help-block">{{ $errors->first("name") }}</span>
                                        @endif
                                    </div>

                                    <div class="form-group @if($errors->has('department_id')) has-error @endif">
                                        <label for="name-field">Shift</label>
                                        <select class="form-control emp_shift" name="shift_id">
                                            @foreach($shifts as $shift)
                                                <option value="{{$shift->id}}" @if($batches['shift_id'] == $shift->id ) selected @endif>{{$shift->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group @if($errors->has('department_id')) has-error @endif">
                                        <label for="name-field">Status</label>
                                        <select class="form-control emp_status" name="status_id">
                                            @foreach($statuses as $status)
                                                <option value="{{$status->id}}" @if($batches['status_id'] == $status->id ) selected @endif>{{$status->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group @if($errors->has('department_id')) has-error @endif">
                                        <label for="name-field">Work type</label>
                                        <select class="form-control work_type" name="work_type_id">
                                            @foreach($work_types as $work_type)
                                                <option value="{{$work_type->id}}" @if($batches['work_type_id'] == $work_type->id ) selected @endif>{{$work_type->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="well well-sm margin-top-50">
                                        <button type="submit" class="btn btn-primary btn-round btn-sm">Reassign Shift</button>
                                        <a class="btn btn-link pull-right" href="{{ route('dept.employeeReassignList') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    
            </form>
        </div>
    </div>
    
@endsection
@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    <script type="text/javascript">
        $(function () {
            $('#toDate').change(function(e){
                $('#fromDate').val('');
                document.getElementById("fromDate").setAttribute("max", $(this).val());
                $('#oToDate').val($(this).val());
            });
            var date =new Date();
            var today = new Date(date.getTime() + 24 * 60 * 60 * 1000);
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!
            var yyyy = today.getFullYear();
            if(dd<10){
                dd='0'+dd
            } 
            if(mm<10){
                mm='0'+mm
            } 
            today = yyyy+'-'+mm+'-'+dd;
            document.getElementById("fromDate").setAttribute("min", today);
            document.getElementById("toDate").setAttribute("min", today);
            document.getElementById("fromDate").setAttribute("max", $('#toDate').val());
        });
    </script>
@endsection