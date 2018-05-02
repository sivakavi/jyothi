@extends('dept.layouts.dept')

@section('title', ' Shift Assign')

@section('content')
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
            <div>Bulk From: <input type="text" id="datepickerFrom"></div>
        </div>
        <div class="col-md-3">
            <div>Bulk To: <input type="text" id="datepickerTo"></div>
        </div>
        <div class="col-md-3">
            <div>Bulk Select: <input type="checkbox" id="isEmpBulkSelected"/></div>
        </div>
    </div>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

    <div class="margin-top-40">
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
        @foreach ($employees as $employee)
            <tr>
                <td><input type="checkbox" class="isEmpSelected"/></td>
                <td class="emp_id">{{ $employee->id }}</td>
                <td class="emp_name">{{ $employee->name }}</td>
                <td class="category">{{ $employee->category->name }}</td>
                <td class="">
                    <select class="form-control emp_shift">
                        @foreach($shifts as $shift)
                            <option value="{{$shift->id}}">{{$shift->allias}}</option>
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
    <div class="well well-sm margin-top-50 text-center">
        <button type="submit" id="shiftAssign" class="btn btn-primary btn-round btn-sm">Assign</button>
    </div>
</div>

@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    <script>
        
        function employeeDetailsArray(data) {
            window.employeeDetails.push(data);
        }
        function dateConversion(dateObj){
        //   var day1 = dateObj.datepicker('getDate').getDate();
        //   var month1 = dateObj.datepicker('getDate').getMonth() + 1;
        //   month1 = ("0" + month1).slice(-2);            
        //   var year1 = dateObj.datepicker('getDate').getFullYear();
        //   return parseInt(""+day1+month1+year1);

          var day1 = dateObj.datepicker('getDate').getDate();
          var month1 = dateObj.datepicker('getDate').getMonth();
          var year1 = dateObj.datepicker('getDate').getFullYear();
          var fullDate = new Date(year1,month1,day1);
          return fullDate;
        }
      $( function() {
        $( "#datepickerFrom" ).datepicker({
            dateFormat: 'dd/mm/yy',
            onSelect: function(dateText, inst) {
                $( "#datepickerTo" ).val('');
                var date = $(this).val();
                
            }
        });
        
        $( "#datepickerTo" ).datepicker({
            dateFormat: 'dd/mm/yy',
            onSelect: function(dateText, inst) {
                var date = $(this).val();
                fromDate = dateConversion($("#datepickerFrom"));
                toDate = dateConversion($(this));
                //console.log(fromDate);
                //console.log(toDate);
                if(fromDate>toDate){
                  alert('From Date must less than To date');
                  $( "#datepickerTo" ).val('');
                  return;
                }
            }
        });

        $( ".empDatepickerFrom" ).datepicker({
            dateFormat: 'dd/mm/yy',
            onSelect: function(dateText, inst) {
              $(this).closest("tr").css({"background-color": "", "color": ""});
              var date = $(this).val();
              var tr = $(this).closest("tr");
              tr.find('.empDatepickerTo').val('');
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

        $('.isEmpSelected').click(function(e){
            var tr = $(this).closest("tr");
            var empDatepickerFrom = tr.find('.empDatepickerFrom').val();
            var empDatepickerTo = tr.find('.empDatepickerTo').val();
            if(empDatepickerFrom=="" || empDatepickerTo=="") {
                alert("Please enter from and to date");
                $(this).prop('checked', false);
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
                  url: "{{route('dept.assignEmpShiftCheck')}}",
                  type: 'GET',
                  data: datas,
                  success:function(data) {
                      if(data==='false'){
                        alert('Already Shift Assigned');
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
        $('#isEmpBulkSelected').click(function(e){
          if($( "#datepickerFrom" ).val()=='' || $( "#datepickerTo" ).val() == ''){
            alert("Please enter from and to date");
            $(this).prop('checked', false);
            return;
          }
          if(this.checked){
            var date = $( "#datepickerFrom" ).val();
            var fromDate = $( "#datepickerTo" ).val();
            var datas = 'empDatepicker='+ date + '&fromDate='+ fromDate;
            jQuery.ajax({
              url: "{{route('dept.bulkSelect')}}",
              type: 'GET',
              data: datas,
              success:function(data) {
                window.empid = data;
                $('#employeeShift > tbody  > tr').each(function() {
                  console.log($(this).find('.emp_id').text());
                  if(jQuery.inArray(parseInt($(this).find('.emp_id').text(),10), empid) === -1){
                    $(this).find('.isEmpSelected').prop('checked', true);
                    datepickerFrom = $( "#datepickerFrom" ).val();
                    datepickerTo = $( "#datepickerTo" ).val();
                    $(this).find('.empDatepickerFrom').val(datepickerFrom);
                    $(this).find('.empDatepickerTo').val(datepickerTo);
                  }
                  else{
                    $(this).css({"background-color": "red", "color": "white"});
                  }
                }); 
              },
            });
          }
          else{
            $('#employeeShift > tbody  > tr').each(function() {
              $(this).find('.isEmpSelected').prop('checked', false);
              $(this).css({"background-color": "", "color": ""});
              $(this).find('.empDatepickerFrom').val('');
              $(this).find('.empDatepickerTo').val('');
            });
          }
        });
        $('#shiftAssign').click(function(e){
            window.employeeDetails = [];
            e.preventDefault();
            if($( "#datepickerFrom" ).val()!='' && $( "#datepickerTo" ).val()){
                var employeeDetails = [];
                $('#employeeShift > tbody  > tr').each(function() {
                    if($(this).find('.isEmpSelected').is(':checked')){
                      emp_id = parseInt($(this).find('.emp_id').text());
                      emp_status = parseInt($(this).find('.emp_status').val());
                      shifts = parseInt($(this).find('.emp_shift').val());
                      work_types = parseInt($(this).find('.work_type').val());
                      empDatepickerFrom = $(this).find('.empDatepickerFrom').val();
                      empDatepickerTo = $(this).find('.empDatepickerTo').val();
                      new_employee = {emp_id: emp_id, shifts: shifts, emp_status: emp_status, work_types: work_types, empDatepickerFrom: empDatepickerFrom, empDatepickerTo: empDatepickerTo};
                      employeeDetailsArray(new_employee);
                    }
                });
                jQuery.ajax({
                  url: "{{route('dept.assignShift')}}",
                  type: 'POST',
                  data: {
                      'employeeDetails' : window.employeeDetails,
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