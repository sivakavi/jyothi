@extends('admin.layouts.admin')

@section('title', 'Shift Update Form')

@section('content')
    <div class="page-header clearfix">
        {{-- <h1><i class="glyphicon glyphicon-edit"></i> shifts / Edit #{{$shift->id}}</h1> --}}
    </div>
    @include('error')

    <div class="row margin-top-30">
        <div class="col-md-8 center-margin">
           <form action="{{ route('admin.shifts.update', $shift->id) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Shift Update Form</h2>
                                <ul class="nav navbar-right">
                                <li class="cursor-pointer"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content margin-top-40">
                                <div class="form-group @if($errors->has('name')) has-error @endif">
                                    <label for="name-field">Name</label>
                                    <input type="text" id="name-field" name="name" class="form-control" value="{{ is_null(old("name")) ? $shift->name : old("name") }}"/>
                                    @if($errors->has("name"))
                                        <span class="help-block">{{ $errors->first("name") }}</span>
                                    @endif
                                </div>

                                <div class="form-group @if($errors->has('department_id')) has-error @endif">
                                    <label for="name-field">Department</label>
                                    <select class="form-control" name="department_id" id="department_id">
                                        <option value="">Select any one Department...</option>
                                        @foreach($departments as $department)
                                            <option @if($shift->department->id == $department->id) selected @endif value="{{$department->id}}">{{$department->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group @if($errors->has('allias')) has-error @endif">
                                    <label for="allias">Allias</label>
                                    <input type="text" id="allias" name="allias" class="form-control" value="{{ is_null(old("allias")) ? $shift->allias : old("allias") }}"/>
                                    @if($errors->has("allias"))
                                        <span class="help-block">{{ $errors->first("allias") }}</span>
                                    @endif
                                </div>

                                <div class="form-group @if($errors->has('in')) has-error @endif">
                                    <label for="in">Allias</label>
                                    <input type="time" id="in" name="in" class="form-control" value="{{ is_null(old("in")) ? $shift->in : old("in") }}"/>
                                    @if($errors->has("in"))
                                        <span class="help-block">{{ $errors->first("in") }}</span>
                                    @endif
                                </div>

                                <div class="form-group @if($errors->has('out')) has-error @endif">
                                    <label for="out">Allias</label>
                                    <input type="time" id="out" name="out" class="form-control" value="{{ is_null(old("out")) ? $shift->out : old("out") }}"/>
                                    @if($errors->has("out"))
                                        <span class="help-block">{{ $errors->first("out") }}</span>
                                    @endif
                                </div>
                                
                                <div class="well well-sm margin-top-50">
                                    <button type="submit" class="btn btn-primary btn-round btn-sm">Update shift</button>
                                    <a class="btn btn-link pull-right" href="{{ route('admin.shifts.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
