@extends('hr.layouts.hr')

@section('title', 'Shift Attendance')
<style>
    .box-style{
        border: 1px solid;
        padding: 20px;
        text-align: center;
        margin-bottom: 15px;
    }
</style>
@section('content')
    <div class="page-header clearfix">
    </div>
    <div class="form-group">
    <label for="department_id">Department</label>
    <select id="department_id" name="department_id" class="form-control" required>
        <option value="">Select any one Department...</option>
        @foreach($departments as $department)
            <option value="{{$department->id}}">{{$department->name}}</option>
        @endforeach
    </select>
    </div>
    <div class="form-group">
        <label for="shift_id">Shift</label>
        <select id = "shift_id" class="form-control" name="shift_id" required>
            <option value="">Select any one Shift...</option>
        </select>
    </div>
    <div class="form-group">
        <label for="date">Date</label><br>
        <input type="date" name="date" id="date" required>
    </div>
    
    <div style="text-align: center;"><button type="submit" class="btn btn-primary btn-round btn-sm empShiftAttendance">Submit</button></div>

@endsection

@section('scripts')
    @parent
    <script>
        $( document ).ready(function() {
            $( "#department_id" ).change(function() {
                var ajaxUrl = "{{ route('hr.getShift') }}";
                $.ajax({
                    url: ajaxUrl,
                    type: 'GET',
                    data: {
                        department_id: $(this).val()
                    },
                    success:function(response) {
                        var $select = $('#shift_id');
                        $select.find('option').remove();
                        $select.append('<option value=' + '' + '>' + 'Select any one Shift...' + '</option>');
                        $.each(response,function(key, value) 
                        {
                            $select.append('<option value=' + key + '>' + value + '</option>');
                        });
                    }
                });
            });
            $( ".empShiftAttendance" ).click(function(e) {
                e.preventDefault();
                var url = "{{ route('hr.shiftDetails') }}";
                // alert(url);
                department_id = $('#department_id').val();
                shift_id = $('#shift_id').val();
                date = $('#date').val();
                if(department_id == "" || shift_id == "" || date == ""){
                    alert("Please enter all fields");
                    return;
                }
                var data = "?department_id=" + department_id + "&shift_id=" + shift_id + "&date=" + date;
                window.location = url+data;
                
            });
        });
    </script>
@endsection