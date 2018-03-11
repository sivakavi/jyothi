@extends('hr.layouts.hr')

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
            <div>From: <input type="text" value="{{ $_GET['fromDate']}}" disabled></div>
        </div>
        <div class="col-md-3">
            <div>To: <input type="text" value="{{ $_GET['toDate']}}" disabled></div>
        </div>
    </div>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <input type="hidden" name="batch_id" id="batch_id" value="{{ $_GET['batch_id']}}">
    <input type="hidden" name="department_id" id="department_id" value="{{ $_GET['batch_id']}}">

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
        @foreach ($assignShifts as $assignShift)
            <tr>
                <td class="emp_id">{{ $assignShift->employee->id }}</td>
                <td class="emp_name">{{ $assignShift->employee->name }}</td>
                <td class="category">{{ $assignShift->employee->category->name }}</td>
                <td class="">
                    <select class="form-control emp_shift">
                        @foreach($shifts as $shift)
                            <option value="{{$shift->id}}" @if($assignShift->shift->id == $shift->id ) selected @endif>{{$shift->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td class="">
                    <select class="form-control emp_status">
                        @foreach($statuses as $status)
                            <option value="{{$status->id}}" @if($assignShift->status->id == $status->id ) selected @endif>{{$status->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td class="">
                    <select class="form-control work_type">
                        @foreach($work_types as $work_type)
                            <option value="{{$work_type->id}}" @if($assignShift->work_type->id == $work_type->id ) selected @endif>{{$work_type->name}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
        @endforeach
    </tbody>
  </table>
    <div class="well well-sm margin-top-50 text-center">
        <button type="submit" id="shiftAssign" class="btn btn-primary btn-round btn-sm">Confirmed</button>
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
        $('#shiftAssign').click(function(e){
            window.employeeDetails = [];
            e.preventDefault();
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
                url: "{{route('hr.assignShift')}}",
                type: 'POST',
                data: {
                    'employeeDetails' : window.employeeDetails,                  
                    'fromDate' : "{{ $_GET['fromDate']}}",
                    'toDate' : "{{ $_GET['toDate']}}",
                    'batch_id' : $( "#batch_id" ).val(),
                    'department_id' : $( "#department_id" ).val(),
                    '_token' : $( "#token" ).val()
                },
                success:function(data) {
                    alert('Shift Confirmed Successfully');
                },
              });
          });
      });

    </script>

@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection