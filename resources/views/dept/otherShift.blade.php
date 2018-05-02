@extends('dept.layouts.dept')

@section('title', 'Shift Details List')

@section('content')
    <div class="page-header clearfix">
    </div>
    <input type="hidden" value="{{ app('request')->input('date') }}" id="shiftDate">
    <input type="hidden" value="{{ app('request')->input('shift_id') }}" id="shiftID">
	<div class="hide">
		<select class="form-control emp_work_type">
			
			@foreach($work_types as $work_type)
				<option value="{{$work_type->id}}">{{$work_type->name}}</option>
			@endforeach
		</select>
		<br>
		<select class="form-control emp_status">
			
			@foreach($statuses as $status)
				<option value="{{$status->id}}">{{$status->name}}</option>
			@endforeach
			</select>
					<br>

		<select class="form-control emp_shift">
			
			@foreach($shifts as $shift)
				<option value="{{$shift->id}}">{{$shift->allias}}</option>
			@endforeach
		</select>
	</div>
	  <div class="pull-right">
	  	From Date : <input type="text" class="empDatepickerFrom">
		To Date : <input type="text" class="empDatepickerTo">
	  	Search by Emp Code (Other Dept.) : <input type="text" name="empName" id="empName">&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-round btn-sm empSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
	  </div>
	  <br>
	  <br>
	  <table id ="records_table" class="table table-bordered"></table>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    <script type="text/javascript">
		function dateConversion(dateObj){
          var day1 = dateObj.datepicker('getDate').getDate();
          var month1 = dateObj.datepicker('getDate').getMonth() + 1;
          month1 = ("0" + month1).slice(-2);            
          var year1 = dateObj.datepicker('getDate').getFullYear();
          return parseInt(""+day1+month1+year1);
        }
    	$(document).on('click', '.empadd', function(e) {
    			e.preventDefault();
    			var tr = $(this).closest("tr");
    			empId = tr.find('.empSearchId').text();
                empDate = $('#shiftDate').val();
    			shift_id = $('#shiftID').val();
    			console.log(empDate);
    			var datas = 'empId='+ empId + '&empDate='+ empDate + '&shift_id='+ shift_id;
    	   		// console.log(datas);
	            jQuery.ajax({
	              url: "{{route('dept.employeeAdd')}}",
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
    	   	$( ".empDatepickerFrom" ).datepicker({
				dateFormat: 'dd/mm/yy',
				onSelect: function(dateText, inst) {
					$('.empDatepickerTo').val('');
				}
			});
			
			$( ".empDatepickerTo" ).datepicker({
				dateFormat: 'dd/mm/yy',
				onSelect: function(dateText, inst) {
				var date = $(this).val();
				
				var fromDate = $('.empDatepickerFrom').val();
				if(fromDate == ""){
					alert('Choose From Date First');
					$('.empDatepickerTo').val('');
					return;
				}
				fromDates = dateConversion($('.empDatepickerFrom'));
				toDates = dateConversion($(this));
				if(fromDates>toDates){
					alert('From Date must less than To date');
					$(this).val('');
					return;
				}
				}
			});
            $(document).on('click', '.empassign', function(e) {
				var fromDate = $('.empDatepickerFrom').val();
				var toDate = $('.empDatepickerTo').val();
				if(fromDate == "" && toDate == ""){
					alert('Choose Date First');
					$('.empDatepickerTo').val('');
					return;
				}
    	   		var tr = $(this).closest("tr");
				var fromDate = $('.empDatepickerFrom').val();
				var toDate = $('.empDatepickerTo').val();
    	   		employee_id = tr.find('.empSearchId').text();
    	   		shift_id = tr.find('.emp_shift').val();
    	   		work_type_id = tr.find('.emp_worktype').val();
    	   		status_id = tr.find('.emp_status').val();
				var datas = 'employee_id='+ employee_id + '&fromDate=' + fromDate + '&toDate=' + toDate + '&employee_id=' + employee_id + '&shift_id=' + shift_id + '&work_type_id=' + work_type_id + '&status_id=' + status_id;
				jQuery.ajax({
						url: "{{route('dept.assignOtherDep')}}",
						type: 'GET',
						data: datas,
						success:function(data) {
							if(data == 'true'){
								alert("Shift Added Successfully");
								location.reload();
							}
							else{
								alert("Already Shift assigned. So Please select different date");
							}
						}
				});
    	   	});
    	   	$('.empSearch').click(function(e){
    	   		e.preventDefault();
				var fromDate = $('.empDatepickerFrom').val();
				var toDate = $('.empDatepickerTo').val();
				if(fromDate == "" && toDate == ""){
					alert('Choose Date First');
					$('.empDatepickerTo').val('');
					return;
				}
    	   		var empName = $('#empName').val();
    	   		var datas = 'name='+ empName;
    	   		if(empName != ''){
    	   			jQuery.ajax({
						url: "{{route('dept.employeeSearch')}}",
						type: 'GET',
						data: datas,
						success:function(data) {
							// response = $.parseJSON(data);
							// console.log(response);
							$('#records_table').hide();
							if(data!=""){
								$('#records_table').show();
								$('#records_table').html('');
								var trHTML = '<tr><th>Employee Id</th><th>Employee Name</th>  <th>Employee Department</th><th> Shift</th><th> Worktype</th><th> Status</th><th></th></tr>';
						        $.each(data, function (i, item) {
						        	trHTML += '';
						            trHTML += '<tr><td class="empSearchId">' + item.id + '</td><td class="emp_name">' + item.name + '</td><td>' + item.department_name + '</td>' +
									'<td><select class="emp_shift">' + $('.emp_shift').html() + '</select></td>' + 
									'<td><select class="emp_worktype">' + $('.emp_work_type').html() + '</select></td>' +
									'<td><select class="emp_status">' + $('.emp_status').html() + '</select></td>' +'<td class=""><button type="button" class="btn btn-primary btn-round btn-sm empassign">Add Employee</button></td></tr>';
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
    	   			alert('please enter employee code');
    	   		}
    	   	});
		});
    </script>
@endsection