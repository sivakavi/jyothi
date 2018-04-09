@extends('admin.layouts.admin')

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif
    <div class="page-header">
        <h1>Users / Create </h1>
        @if(app('request')->input('role')=="student")
            <a href="{{ asset('excel/user.xlsx') }}">Sample User Excel</a>
            <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 30px;" action="{{ route('admin.users.importExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="file" name="import_file" />
                @if($errors->has("import_file"))
                    <span class="help-block">{{ $errors->first("import_file") }}</span>
                @endif
                <br/>
                <button class="btn btn-primary">Import File</button>
            </form>
        @endif
    </div>
    @include('error')
    <form class="form-horizontal" method="post" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group">
            <label class="control-label col-sm-3" for="fname">Name:</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="email">Email:</label>
            <div class="col-sm-6">
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter email" required>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="password">Password:</label>
            <div class="col-sm-6">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="manager">Role:</label>
            <div class="col-sm-6">
                <input id="HR" type="radio" name="role" value="hr" required> <label for="HR">HR</label>
                <input id="Dept" type="radio" name="role" value="dept"> <label for="Dept">Department</label>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="confirmed   ">Active:</label>
            <div class="col-sm-6">
                <input id="active" type="radio" name="active" value="1" required> <label for="active">Yes</label>
                <input id="inactive" type="radio" name="active" value="0"> <label for="inactive">No</label>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="confirmed">Confirmed:</label>
            <div class="col-sm-6">
                <input id="yes" type="radio" name="confirmed" value="1" required> <label for="yes">Yes</label>
                <input id="no" type="radio" name="confirmed" value="0"> <label for="no">No</label>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="confirmed">Department:</label>
            <div class="col-sm-6">
                <select class="form-control" name="department_id" id="department_id" required>
                    <option value="">Select any one Department...</option>
                    @foreach($departments as $department)
                        <option value="{{$department->id}}">{{$department->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <br/>
        <br/>
        <div class="form-group"> 
            <div class="col-sm-12">
                <center><button type="submit" class="btn btn-success">Submit</button></center>
            </div>
        </div>
    </form>
@endsection