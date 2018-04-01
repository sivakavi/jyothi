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
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <input type="hidden" name="department_id" id="department_id" value="{{ $_GET['department_id']}}">
    <div class="margin-top-40">
    <div class="col-md-3">
            <div>Bulk Select: <input type="checkbox" id="isEmpBulkSelected"/></div>
        </div>
    <table class="table table-bordered" id="employeeShift">
    <thead>
      <tr>
        <th></th>
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
        @foreach ($assignShifts as $assignShift)
            <tr>
                <td><input type="checkbox" class="isEmpSelected"/></td>
                <td class="emp_id">{{ $assignShift['employee_id'] }}</td>
                <td class="emp_name">{{ $assignShift['employee_name'] }}</td>
                <td class="category">{{ $assignShift['category_name'] }}</td>
                <td class="">
                    <select class="form-control emp_shift">
                        @foreach($shifts as $shift)
                            <option value="{{$shift->id}}" @if($assignShift['shift_id'] == $shift->id ) selected @endif>{{$shift->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td class="">
                    <select class="form-control emp_status">
                        @foreach($statuses as $status)
                            <option value="{{$status->id}}" @if($assignShift['status_id'] == $status->id ) selected @endif>{{$status->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td class="">
                    <select class="form-control work_type">
                        @foreach($work_types as $work_type)
                            <option value="{{$work_type->id}}" @if($assignShift['work_type_id'] == $work_type->id ) selected @endif>{{$work_type->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td class="">
                    <input type="text" class="empDatepickerFrom" value="{{ date('m/d/Y', strtotime($assignShift['fromDate'])) }}">
                </td>
                <td class="">
                    <input type="text" class="empDatepickerTo" value="{{ date('m/d/Y', strtotime($assignShift['toDate'])) }}">
                </td>
                <td class="batch_id hide">{{ $assignShift['id'] }}</td>
                <td class="">
                    <button type="submit" class="btn btn-primary btn-round btn-sm empShiftAssign">Confirmed</button>
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
        function dateConversion(dateObj){
          var day1 = dateObj.datepicker('getDate').getDate();
          var month1 = dateObj.datepicker('getDate').getMonth() + 1;
          month1 = ("0" + month1).slice(-2);            
          var year1 = dateObj.datepicker('getDate').getFullYear();
          return parseInt(""+day1+month1+year1);
        }
      $( function() {
        $( ".empDatepickerFrom" ).datepicker({
            onSelect: function(dateText, inst) {
            }
        });
        $( ".empDatepickerTo" ).datepicker({
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
            var batch_id = parseInt(tr.find('.batch_id').text());
            // console.log(batch_id);return;
            if(empDatepickerFrom!="" && empDatepickerTo!=""){
              var datas = 'empDatepickerFrom='+ empDatepickerFrom  + '&empDatepickerTo='+ empDatepickerTo + '&employee_id='+ employee_id + '&work_type_id=' + work_type_id + '&shift_id=' + shift_id + '&status_id=' + status_id + '&batch_id=' + batch_id;
              jQuery.ajax({
                  url: "{{route('hr.assignEmpShiftCheck')}}",
                  type: 'GET',
                  data: datas,
                  success:function(data) {
                    if(data==='false'){
                        alert('Already Shift Assigned different date');
                    }
                    else{
                        alert('Shift Confirmed Successfully');
                        location.reload();
                    }
                  },
                });
            }
            else{
              alert('Please fill From Date and To date');
            }
        });

        $('#isEmpBulkSelected').click(function(e){
          if(this.checked){
            $('#employeeShift > tbody  > tr').each(function() {
              $(this).find('.isEmpSelected').prop('checked', true);
            });
          }
          else{
            $('#employeeShift > tbody  > tr').each(function() {
              $(this).find('.isEmpSelected').prop('checked', false);
            });
          }
        });

        $('#shiftAssign').click(function(e){
          window.employeeDetails = [];
          e.preventDefault();
          var employeeDetails = [];
          $('#employeeShift > tbody  > tr').each(function() {
            if($(this).find('.isEmpSelected').is(':checked')){
              emp_id = parseInt($(this).find('.emp_id').text());
              batch_id = parseInt($(this).find('.batch_id').text());
              emp_status = parseInt($(this).find('.emp_status').val());
              shifts = parseInt($(this).find('.emp_shift').val());
              work_types = parseInt($(this).find('.work_type').val());
              empDatepickerFrom = $(this).find('.empDatepickerFrom').val();
              empDatepickerTo = $(this).find('.empDatepickerTo').val();
              new_employee = {batch_id: batch_id, emp_id: emp_id, shifts: shifts, emp_status: emp_status, work_types: work_types, empDatepickerFrom: empDatepickerFrom, empDatepickerTo: empDatepickerTo};
              employeeDetailsArray(new_employee);
            }
          });
          if(window.employeeDetails == ""){
              alert("Please select any employee");
              return;
          }
          
          jQuery.ajax({
                url: "{{route('hr.bulkConfirmedShift')}}",
                type: 'POST',
                data: {
                    'employeeDetails' : window.employeeDetails,
                    '_token' : $( "#token" ).val()
                },
                success:function(data) {
                    alert('Shift Confirmed Successfully');
                    location.reload();
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