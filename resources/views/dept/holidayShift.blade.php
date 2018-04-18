@extends('dept.layouts.dept')

@section('title', 'Holiday Shift Assign')

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
        <th>From Date</th>
        <th>To Date</th>
        <th></th>
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
                <td class="">
                    <input type="text" class="empDatepickerFrom">
                </td>
                <td class="">
                    <input type="text" class="empDatepickerTo">
                </td>
                <td class="">
                    <button type="submit" class="empShiftAssign" class="btn btn-primary btn-round btn-sm">Assign</button>
                </td>
                
            </tr>
        @endforeach
    </tbody>
  </table>
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
        function dateConversion(dateObj){
          var day1 = dateObj.datepicker('getDate').getDate();
          var month1 = dateObj.datepicker('getDate').getMonth();
          var year1 = dateObj.datepicker('getDate').getFullYear();
          var fullDate = new Date(year1,month1,day1);
          return fullDate;
        }
      $( function() {
        $( ".empDatepickerFrom" ).datepicker({
            dateFormat: 'dd/mm/yy',
            onSelect: function(dateText, inst) {
              $(this).closest("tr").css({"background-color": "", "color": ""});
              var date = $(this).val();
              var tr = $(this).closest("tr");
              var emp_id = tr.find('.emp_id').text();
              var datas = 'empDatepicker='+ date + '&emp_id='+ emp_id;
              jQuery.ajax({
                  url: "{{route('dept.assignEmpShiftIndividual')}}",
                  type: 'GET',
                  data: datas,
                  success:function(data) {
                      if(data==='false'){
                        alert('Already Shift Assigned');
                        tr.find('.empDatepickerFrom').val('');
                        tr.find('.empDatepickerTo').val('');
                      }
                  },
                });
            }
        });
        $( ".empDatepickerTo" ).datepicker({
            dateFormat: 'dd/mm/yy',
            onSelect: function(dateText, inst) {
              $(this).closest("tr").css({"background-color": "", "color": ""});
              var date = $(this).val();
              var tr = $(this).closest("tr");
              var fromDate = tr.find('.empDatepickerFrom').val();
              if(fromDate == ""){
                alert('Choose From Date First');
                tr.find('.empDatepickerTo').val('');
                return;
              }
              fromDates = dateConversion(tr.find('.empDatepickerFrom'));
              toDates = dateConversion($(this));
              if(fromDates>toDates){
                alert('From Date must less than To date');
                $(this).val('');
                return;
              }
              var emp_id = tr.find('.emp_id').text();
              var datas = 'empDatepicker='+ date + '&emp_id='+ emp_id + '&fromDate='+ fromDate;
              jQuery.ajax({
                  url: "{{route('dept.assignEmpShiftIndividual')}}",
                  type: 'GET',
                  data: datas,
                  success:function(data) {
                      if(data==='false'){
                        alert('Already Shift Assigned');
                        tr.find('.empDatepickerTo').val('');
                        tr.find('.empDatepickerFrom').val('');
                      }
                  },
                });
            }
        });
        $('.empShiftAssign').click(function(e){
            e.preventDefault();
            var tr = $(this).closest("tr");
            var empDatepickerFrom = tr.find('.empDatepickerFrom').val();
            var empDatepickerTo = tr.find('.empDatepickerTo').val();
            var employee_id = tr.find('.emp_id').text();
            var status_id = parseInt(tr.find('.emp_status').val());
            var shift_id = parseInt(tr.find('.emp_shift').val());
            var work_type_id = parseInt(tr.find('.work_type').val());
            if(empDatepickerFrom!="" && empDatepickerTo!=""){
              var datas = 'empDatepickerFrom='+ empDatepickerFrom  + '&empDatepickerTo='+ empDatepickerTo + '&employee_id='+ employee_id + '&work_type_id=' + work_type_id + '&shift_id=' + shift_id + '&status_id=' + status_id;
              jQuery.ajax({
                  url: "{{route('dept.holidayShiftAssign')}}",
                  type: 'GET',
                  data: datas,
                  success:function(data) {
                      if(data==='false'){
                        alert('This is not a Holiday');
                      }
                      else{
                        alert('Shift Assigned Successfully');
                      }
                      tr.find('.empDatepickerFrom').val('');
                      tr.find('.empDatepickerTo').val('');
                  },
                });
            }
            else{
              alert('Please fill From Date and To date');
            }
        });
      });

    </script>

@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection