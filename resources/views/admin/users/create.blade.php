@extends('admin.layouts.admin')

@section('title', 'Users / Create')

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif
    <div class="page-header">
    </div>
    @include('error')
    <div class="margin-top-30">
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
                <input id="Dept" type="radio" name="role" value="dept" checked> <label for="Dept">Department</label>
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

        <div class="form-group department">
            <label class="control-label col-sm-3" for="confirmed">Department:</label>
            <div class="col-sm-6">
                <select class="form-control" name="department_id" id="department_id" required>
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
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $( document ).ready(function() {
            $("input[name$='role']").click(function() {
                var test = $(this).val();
                if(test == "dept"){
                    $(".department").show();
                }
                else{
                    $(".department").hide();
                }
            });     
        });
    </script>
@endsection