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
            <div>Bulk From: <input type="text" id="datepickerFrom"></div>
        </div>
        <div class="col-md-3">
            <div>Bulk To: <input type="text" id="datepickerTo"></div>
        </div>
        <button type="button" class="btn btn-primary btn-round btn-sm empSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
    </div>

    <div class="margin-top-40">
    <table id ="records_table" class="table table-bordered"></table>
</div>

@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        function dateConversion(dateObj){
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
                if($( "#datepickerFrom" ).val() == ''){
                  alert('From Date is must');
                  $( "#datepickerTo" ).val('');
                  return;  
                }
                var date = $(this).val();
                fromDate = dateConversion($("#datepickerFrom"));
                toDate = dateConversion($(this));
                if(fromDate>toDate){
                  alert('From Date must less than To date');
                  $( "#datepickerTo" ).val('');
                  return;
                }
            }
        });

        $('.empSearch').click(function(e){
            e.preventDefault();
            var fromDate = $('#datepickerFrom').val();
            var toDate = $('#datepickerTo').val();
            if(fromDate == "" && toDate == ""){
                alert('Choose Date First');
                return;
            }
            var datas = 'fromDate='+ fromDate + '&toDate='+ toDate;
            jQuery.ajax({
                url: "{{route('dept.shiftDetailPrint')}}",
                type: 'GET',
                data: datas,
                success:function(data) {
                    $('#records_table').hide();
                    if(data!=""){
                        $('#records_table').show();
                        $('#records_table').html('');
                        var trHTML = '<tr><th>Date</th><th>Employee Code</th><th>Employee Name</th><th>Shift Code</th><th>Shift Name</th><th>Department Code</th><th>Department Name</th></tr>';
                        $.each(data, function (i, item) {
                            trHTML += '';
                            trHTML += '<tr><td>' + item.date + '</td><td class="emp_name">' + item.emp_code + '</td><td>' + item.emp_name + '</td><td>' + item.shift_code + '</td><td>' + item.shift_name + '</td><td>' + item.department_code + '</td><td>' + item.department_name + '</td></tr>';
                        });

                        $('#records_table').append(trHTML);
                    }
                    else{
                        alert('Data Not Found');
                    }
                    
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