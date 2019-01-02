@extends('admin.layouts.admin')

@section('title', 'Employee Unexpected Holiday')
<!-- <style>
    .box-style{
        border: 1px solid;
        padding: 20px;
        text-align: center;
        margin-bottom: 15px;
    }
</style> -->
@section('content')
    <div class="page-header clearfix">
    </div>
    <div class="row margin-top-30">
    <div class="col-md-8 center-margin">
    @if (session('message'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <p>{{{ session('message') }}}</p>
        </div>
    @endif
    <div class="form-group">
        <label for="date">Date</label><br>
        <input type="date" name="date" id="date" required>
    </div>
    
    <div class="form-group">
        <label for="reason">Reason</label><br>
        <input type="text" name="reason" id="reason" required>
    </div>

    <div style="text-align: center;"><button type="submit" class="btn btn-primary btn-round btn-sm empHoliday">Submit</button></div>
</div>
</div>
@endsection

@section('scripts')
    @parent
    <script>
        $( document ).ready(function() {
            $( ".empHoliday" ).click(function(e) {
                e.preventDefault();
                var url = "{{ route('admin.unexpectedHoliday') }}";
                // alert(url);
                reason = $('#reason').val();
                date = $('#date').val();
                if(reason == "" || date == ""){
                    alert("Please enter all fields");
                    return;
                }
                var data = "?reason=" + reason + "&date=" + date;
                window.location = url+data;
                
            });
        });
    </script>
@endsection