@extends('dept.layouts.dept')

@section('title', 'Shift Details List')

@section('content')
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
				<option value="{{$shift->id}}">{{$shift->name}}</option>
			@endforeach
		</select>
	</div>
	  <div class="pull-right">
	  	Search by Emp Code : <input type="text" name="empName" id="empName">&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-round btn-sm empSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
	  </div>
	  <br>
	  <br>
	  <table id ="records_table" class="table table-bordered"></table>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
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
            $(document).on('click', '.empassign', function(e) {
    	   		var tr = $(this).closest("tr");
    	   		$('#assignShift').val(tr.find('.assign_shift_id').text());
    	   		$('.modal-title').text(tr.find('.emp_name').text()+' - '+ tr.find('.empStatus').text());
                empId = tr.find('.empSearchId').text();
                $('#emp_id').val(empId);
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
								var trHTML = '<tr><th>Employee Id</th><th>Employee Name</th>  <th>Employee Department</th><th> Shift</th><th> Worktype</th><th> Status</th><th> From Date</th><th>  To Date</th><th></th></tr>';
						        $.each(data, function (i, item) {
						        	trHTML += '';
						            trHTML += '<tr><td class="empSearchId">' + item.id + '</td><td class="emp_name">' + item.name + '</td><td>' + item.department_name + '</td>' +
									'<td><select class="emp_worktype">' + $('.emp_shift').html() + '</select></td>' + 
									'<td><select class="emp_worktype">' + $('.emp_work_type').html() + '</select></td>' +
									'<td><select class="emp_status">' + $('.emp_status').html() + '</select></td>' +
									'<td class=""><input type="text" class="empDatepickerFrom"></td><td class=""><input type="text" class="empDatepickerTo"></td>' +
									'<td class=""><button type="button" class="btn btn-primary btn-round btn-sm empassign">Add Employee</button></td></tr>';
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
    	   			alert('please enter name');
    	   		}
    	   	});
		});
    </script>
@endsection