@extends('admin.layouts.admin')

@section('title', 'WorkType Update Form')

@section('content')
    <div class="page-header clearfix">
        {{-- <h1><i class="glyphicon glyphicon-edit"></i> work_types / Edit #{{$work_type->id}}</h1> --}}
    </div>
    @include('error')

    <div class="row margin-top-30">
        <div class="col-md-8 center-margin">
           <form action="{{ route('admin.work_types.update', $work_type->id) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>WorkType Update Form</h2>
                                <ul class="nav navbar-right">
                                <li class="cursor-pointer"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content margin-top-40">
                                <div class="form-group @if($errors->has('name')) has-error @endif">
                                    <label for="name-field">Name</label>
                                    <input type="text" id="name-field" name="name" class="form-control" value="{{ is_null(old("name")) ? $work_type->name : old("name") }}"/>
                                    @if($errors->has("name"))
                                        <span class="help-block">{{ $errors->first("name") }}</span>
                                    @endif
                                </div>

                                <div class="form-group @if($errors->has('department_id')) has-error @endif">
                                    <label for="name-field">Department</label>
                                    <select class="form-control" name="department_id" id="department_id">
                                        <option value="">Select any one Department...</option>
                                        @foreach($departments as $department)
                                            <option @if($work_type->department->id == $department->id) selected @endif value="{{$department->id}}">{{$department->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="well well-sm margin-top-50">
                                    <button type="submit" class="btn btn-primary btn-round btn-sm">Update WorkType</button>
                                    <a class="btn btn-link pull-right" href="{{ route('admin.work_types.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
