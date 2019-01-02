@extends('admin.layouts.admin')

@section('title', ' ReAssign Shift')

@section('content')
    <div class="page-header clearfix"></div>
    <br/>
    <br/>
    <div>
        <center>Search by Emp Code : <input type="text" name="empName" id="empName">&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-round btn-sm empSearch"><i class="fa fa-search" aria-hidden="true"></i></button></center>
    </div>
    <table id ="records_table" class="table table-bordered"></table>
@endsection
@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    <script type="text/javascript">
        $(function () {
            $('.empSearch').click(function(e){
                e.preventDefault();
                var empName = $('#empName').val();
                var datas = 'name='+ empName;
                if(empName != ''){
                    jQuery.ajax({
                        url: "{{route('admin.employeeBatchSearch')}}",
                        type: 'GET',
                        data: datas,
                        success:function(data) {
                            $('#records_table').hide();
                            if(data!=""){
                                $('#records_table').show();
                                $('#records_table').html('');
                                var trHTML = '<tr><th>Batch Id</th>  <th>From Date</th><th>To Date</th><th></th></tr>';
                                $.each(data, function (i, item) {
                                    trHTML += '';
                                    trHTML += '<tr><td class="empSearchId">' + item.id + '</td><td>' + $.datepicker.formatDate('dd/mm/yy', new Date(item.fromDate)) + '</td><td>' + $.datepicker.formatDate('dd/mm/yy', new Date(item.toDate)) + '</td><td class=""><button type="button" class="btn btn-primary btn-round btn-sm empreassign">Reassign  Employee</button></td></tr>';
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
             $(document).on('click', '.empreassign', function(e) {
    	   		var tr = $(this).closest("tr");
    	   		batchId = tr.find('.empSearchId').text();
    	   		location.href = "{{ route('admin.employeeReassign') }}" + "?batch_id=" + batchId;
    	   	});
            $('#empName').keypress(function(e){
                if(e.which == 13){//Enter key pressed
                    $('.empSearch').click();//Trigger search button click event
                }
            });
        });
    </script>
@endsection