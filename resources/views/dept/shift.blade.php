@extends('dept.layouts.dept')

@section('title', ' Shift Assign')

@section('content')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
.tile-stats
{
    border: 1px solid #2a3f54;
}
#box1
{
    border: 1px solid #2a3f54;

}


</style>

    <div class="page-header clearfix"></div>

    <div class="row top_tiles margin-top-40">
        <div class="col-md-3">
            <div>From: <input type="text" id="datepickerFrom"></div>
        </div>
        <div class="col-md-3">
            <div>To: <input type="text" id="datepickerTo"></div>
        </div>
    </div>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

    <div class="margin-top-40">
    <table class="table table-bordered" id="employeeShift">
    <thead>
      <tr>
        <th>Employee Id</th>
        <th>Employee Name</th>
        <th>Category</th>
        <th>Shift</th>
        <th>Status</th>
        <th>Work Type</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($employees as $employee)
            <tr>
                <td class="emp_id">{{ $employee->id }}</td>
                <td class="emp_name">{{ $employee->name }}</td>
                <td class="category">{{ $employee->category->name }}</td>
                <td class="">
                    <select class="form-control emp_shift">
                        @foreach($shifts as $shift)
                            <option value="{{$shift->id}}">{{$shift->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td class="">
                    <select class="form-control emp_status">
                        @foreach($statuses as $status)
                            <option value="{{$status->id}}">{{$status->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td class="">
                    <select class="form-control work_type">
                        @foreach($work_types as $work_type)
                            <option value="{{$work_type->id}}">{{$work_type->name}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
        @endforeach
    </tbody>
  </table>
    <div class="well well-sm margin-top-50 text-center">
        <button type="submit" id="shiftAssign" class="btn btn-primary btn-round btn-sm">Assign</button>
    </div>
</div>

@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        
        function employeeDetailsArray(data) {
            window.employeeDetails.push(data);
        }
      $( function() {
        $( "#datepickerFrom" ).datepicker({
            onSelect: function(dateText, inst) {
                var date = $(this).val();
                jQuery.ajax({
                  url: "{{route('dept.assignShiftCheck')}}",
                  type: 'GET',
                  data: {
                      'date' : date
                  },
                  success:function(data) {
                      if(data==='false'){
                        alert('Already Shift Assigned');
                        $( "#datepickerFrom" ).val('');
                      }
                  },
                });
            }
        });
        $( "#datepickerTo" ).datepicker({
            onSelect: function(dateText, inst) {
                var date = $(this).val();
                jQuery.ajax({
                  url: "{{route('dept.assignShiftCheck')}}",
                  type: 'GET',
                  data: {
                      'date' : date
                  },
                  success:function(data) {
                      if(data==='false'){
                        alert('Already Shift Assigned');
                        $( "#datepickerTo" ).val('');
                      }
                  },
                });
            }
        });
        $('#shiftAssign').click(function(e){
            window.employeeDetails = [];
            e.preventDefault();
            if($( "#datepickerFrom" ).val()!='' && $( "#datepickerTo" ).val()){
                var employeeDetails = [];
                $('#employeeShift > tbody  > tr').each(function() {
                    emp_id = parseInt($(this).find('.emp_id').text());
                    emp_status = parseInt($(this).find('.emp_status').val());
                    shifts = parseInt($(this).find('.emp_shift').val());
                    work_types = parseInt($(this).find('.work_type').val());
                    new_employee = {emp_id: emp_id, shifts: shifts, emp_status: emp_status, work_types: work_types};
                    employeeDetailsArray(new_employee);
                });
                jQuery.ajax({
                  url: "{{route('dept.assignShift')}}",
                  type: 'POST',
                  data: {
                      'employeeDetails' : window.employeeDetails,                  
                      'fromDate' : $( "#datepickerFrom" ).val(),
                      'toDate' : $( "#datepickerTo" ).val(),
                      '_token' : $( "#token" ).val()
                  },
                  success:function(data) {
                      alert('Shift Assigned Successfully');
                      location.reload();
                  },
                });
            }
            else{
                alert('Please fill from and to date');
            }
        });
      });

    </script>

@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection