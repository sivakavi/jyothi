@extends('hr.layouts.hr')

@section('title', 'Shift Details List')

@section('content')
    <div class="page-header clearfix">
    </div>
    <input type="hidden" value="{{ app('request')->input('date') }}" id="shiftDate">
    <input type="hidden" value="{{ app('request')->input('department_id') }}" id="department_id">
    <input type="hidden" value="{{ app('request')->input('shift_id') }}" id="shiftID">
    <div class="modal fade" id="myModal" role="dialog">
	    <div class="modal-dialog">
	    
	      <!-- Modal content-->
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal">&times;</button>
	          <h4 class="modal-title"></h4>
              <input type="hidden" id="emp_id" value="">
              <input type="hidden" id="bulk_change" value="0">
	        </div>
	        <div class="modal-body">
	        	<input type="hidden" id="assignShift">
                <select class="form-control emp_work_type">
                    <option value=""> Please Select Work Type</option>
                    @foreach($work_types as $work_type)
                        <option value="{{$work_type->id}}">{{$work_type->name}}</option>
                    @endforeach
                </select>
                <br>
	          	<select class="form-control emp_status">
	          		<option value=""> Please Select Status</option>
	                @foreach($statuses as $status)
	                    <option value="{{$status->id}}">{{$status->name}}</option>
	                @endforeach
	            </select>
	            <br>

	            <select class="form-control emp_leave hide">
	          		<option value=""> Please Select Leave Type</option>
	                @foreach($leaves as $leave)
	                    <option value="{{$leave->id}}">{{$leave->name}}</option>
	                @endforeach
	            </select>

	            <div class="hide othours">
	            	OT Hours : <input type="number" name="othours" id="othours">
	            </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-default saveModal">Save and Close</button>
	          <button type="button" class="btn btn-default closeModal" data-dismiss="modal">Close</button>
	        </div>
	      </div>
	      
	    </div>
	  </div>
	  <div class="pull-right">
	  	Search by Emp Code : <input type="text" name="empName" id="empName">&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-round btn-sm empSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
	  </div>
	  <br>
	  <br>
	  <table id ="records_table" class="table table-bordered"></table>
    <table class="table table-bordered" id="employeeShift">
    <thead>
      <tr>
        <th>Employee Id</th>
        <th>Employee Name</th>
        <th>Category</th>
        <th>Employee Department</th>
        <th>Assigned Department</th>
        <th>Work Type</th>
        <th>Status</th>
        <th>Leave</th>
        <th>OtHours</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
        @foreach ($employees as $empShift)
            <tr>
                <td class="assign_shift_id hide">{{ $empShift->id }}</td>
                <td class="emp_id">{{ $empShift->employee->id }}</td>
                <td class="emp_name">{{ $empShift->employee->name }}</td>
                <td class="category">{{ $empShift->employee->category->name }}</td>
                <td class="">
                    {{ $empShift->employee->department->name }}
                </td>
                <td class="">
                	@if($empShift->changed_department)
                    	{{ $empShift->changed_department->name }}
                    @else
                    	{{ $empShift->employee->department->name }}
                    @endif
                </td>
                
                <td class="">
                    {{ $empShift->work_type->name }}
                </td>

                <td class="empStatus">
                    {{ $empShift->status->name }}
                </td>

                
                <td class="">
                	@if($empShift->leave)
                    	{{ $empShift->leave->name }}
                    @endif
                </td>
                <td class="">
                    {{ $empShift->otHours }}
                </td>
                <td class="">
                    <button type="button" class="btn btn-primary btn-round btn-sm empassign">Change</button>
                </td>
            </tr>
        @endforeach
    </tbody>
  </table>
<div class="pull-right">
    {{ $employees->links() }}
</div>
<div class="text-center">
	<button type="button" id="empbulkassign" class="btn btn-primary btn-round btn-sm">Bulk Change</button>
</div>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    <script type="text/javascript">
    	$(document).on('click', '.empadd', function(e) {
    			e.preventDefault();
    			var tr = $(this).closest("tr");
    			empId = tr.find('.empSearchId').text();
                empDate = $('#shiftDate').val();
                shift_id = $('#shiftID').val();
    			department_id = $('#department_id').val();
    			console.log(empDate);
                 + '&department_id='+ department_id
                var datas = 'empId='+ empId + '&empDate='+ empDate + '&shift_id='+ shift_id + '&department_id='+ department_id;
    	   		// console.log(datas);
	            jQuery.ajax({
	              url: "{{route('hr.employeeAdd')}}",
	              type: 'GET',
	              data: datas,
	              success:function(data) {
	                  if(data==='true'){
	                    alert('Employee Added Successfully');
	                    location.reload();
	                  }
	                  else{
	                  	alert('Employee Shift Not Assingned this date');
	                  }
	              },
                });
    		});
    	$(function () {
    	   	$('.saveModal').click(function(){
                if($('.emp_status').val() !="" && $('.emp_work_type').val() !="")
                {
                    department_id = $('#department_id').val();
                    status = $('.emp_status').find("option:selected").text();
                    leave = false;
                    othours = false;
                    assignShiftId = $('#assignShift').val();
                    ajaxURL = "{{route('hr.shiftDetailsChange')}}";
                    if(assignShiftId == ""  && $('#bulk_change').val() == 0){
                        assignShiftId = 0;
                        ajaxURL = "{{route('hr.employeeAdd')}}";
                    }
                    else if($('#bulk_change').val() == 1){
                        ajaxURL = "{{route('hr.shiftBulkDetailsChange')}}";
                    }
                    empDate = $('#shiftDate').val();
                    empId = $('#emp_id').val();
                    shift_id = $('#shiftID').val();
                    if(status == 'Leave'){
                        leave = $('.emp_leave').val();
                    }
                    else if(status == 'OT'){
                        othours = $('#othours').val();
                    }
                    status = $('.emp_status').val();
                    emp_work_type = $('.emp_work_type').val();
                    var datas = 'status='+ status + '&leave='+ leave + '&assignShiftId='+ assignShiftId + '&othours='+ othours + '&emp_work_type='+ emp_work_type + '&empDate='+ empDate + '&emp_id='+ empId + '&shift_id='+ shift_id + '&department_id='+ department_id;
                    // console.log(datas);
                    jQuery.ajax({
                      url: ajaxURL,
                      type: 'GET',
                      data: datas,
                      success:function(data) {
                          if(data==='true'){
                            alert('Record Changed Successfully');
                            $('#myModal').modal('toggle');
                            location.reload();
                          }
                      },
                    });
                } else{
                    alert("Please select status and worktype");
                }
    	   	});
    	   	 $(document).on('click', '.empassign', function(e) {
    	   		var tr = $(this).closest("tr");
                $('#assignShift').val(tr.find('.assign_shift_id').text());
                $('.modal-title').text(tr.find('.emp_name').text()+' - '+ tr.find('.empStatus').text());
                empId = tr.find('.empSearchId').text();
                $('#emp_id').val(empId);
                $('#myModal').modal('toggle');
    	   	});
            $(document).on('click', '.close, .closeModal', function(e) {
				$('#bulk_change').val('0');
			});
			$(document).on('click', '#empbulkassign', function(e) {
				$('#bulk_change').val('1');
				$('.modal-title').text('Bulk Changes')
				$('#myModal').modal('toggle');
			});
    	   	$('.emp_status').on('change', function() {
    	   		var status = $(this).find("option:selected").text();
    	   		$('.othours').addClass('hide');
    	   		$('.emp_leave').addClass('hide');
    	   		if(status == 'Leave'){
    	   			$('.emp_leave').removeClass('hide');
    	   		}
    	   		if(status == 'OT'){
    	   			$('.othours').removeClass('hide');
    	   		}
    	   	})
    	   	$('.empSearch').click(function(e){
    	   		e.preventDefault();
    	   		var empName = $('#empName').val();
                department_id = $('#department_id').val();
    	   		var datas = 'name='+ empName + '&department_id='+ department_id;
    	   		if(empName != ''){
    	   			jQuery.ajax({
						url: "{{route('hr.employeeSearch')}}",
						type: 'GET',
						data: datas,
						success:function(data) {
							// response = $.parseJSON(data);
							// console.log(response);
							$('#records_table').hide();
							if(data!=""){
								$('#records_table').show();
								$('#records_table').html('');
								var trHTML = '<tr><th>Employee Id</th><th>Employee Name</th>  <th>Employee Department</th><th></th></tr>';
						        $.each(data, function (i, item) {
						        	trHTML += '';
						            trHTML += '<tr><td class="empSearchId">' + item.id + '</td><td class="emp_name">' + item.name + '</td><td>' + item.department_name + '</td><td class=""><button type="button" class="btn btn-primary btn-round btn-sm empassign">Add Employee</button></td></tr>';
						        });

						        $('#records_table').append(trHTML);
							}
							else{
								alert('Data Not Found');
							}
							
		              },
	                });
    	   		}
    	   		else{
    	   			alert('please enter employee code fully...');
    	   		}
    	   	});
		});
    </script>
@endsection