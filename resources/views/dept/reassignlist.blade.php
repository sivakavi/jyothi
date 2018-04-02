@extends('dept.layouts.dept')

@section('title', ' ReAssign Shift')

@section('content')
    <div class="page-header clearfix"></div>
    <div class="pull-right">
        <input type="text" name="empName" id="empName"><button type="button" class="btn btn-primary btn-round btn-sm empSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
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
                        url: "{{route('dept.employeeBatchSearch')}}",
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
                                    trHTML += '<tr><td class="empSearchId">' + item.id + '</td><td>' + item.fromDate + '</td><td>' + item.toDate + '</td><td class=""><button type="button" class="btn btn-primary btn-round btn-sm empreassign">Reassign  Employee</button></td></tr>';
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
             $(document).on('click', '.empreassign', function(e) {
    	   		var tr = $(this).closest("tr");
    	   		batchId = tr.find('.empSearchId').text();
    	   		location.href = "{{ route('dept.employeeReassign') }}" + "?batch_id=" + batchId;
    	   	});
        });
    </script>
@endsection