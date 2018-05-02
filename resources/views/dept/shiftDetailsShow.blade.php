@extends('dept.layouts.dept')

@section('title', ' Shift Print')

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

#records_table {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

#records_table td, #records_table th {
    border: 1px solid #ddd;
    padding: 8px;
}

#records_table tr:nth-child(even){background-color: #f2f2f2;}

#records_table tr:hover {background-color: #ddd;}

#records_table th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #4CAF50;
    color: white;
}


</style>

    <div class="page-header clearfix"></div>

    <div class="row top_tiles margin-top-40">
        <div class="col-md-3">
            <div>Shift From: <input type="text" id="datepickerFrom"></div>
        </div>
        <div class="col-md-3">
            <div>Shift To: <input type="text" id="datepickerTo"></div>
        </div>
        <button type="button" class="btn btn-primary btn-round btn-sm empSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
    </div>

    <div class="margin-top-40">
    <table id ="records_table" class="table table-bordered"></table>
    <br/>
    <br/>
    <center><button id="printtable" type="button" style="display:none" class="btn btn-success">Print</button></center>
</div>

@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
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
                            var today = new Date(item.date);
                            var dd = today.getDate();
                            var mm = today.getMonth()+1;

                            var yyyy = today.getFullYear();
                            if(dd<10){
                                dd='0'+dd;
                            } 
                            if(mm<10){
                                mm='0'+mm;
                            } 
                            var today = dd+'-'+mm+'-'+yyyy;
                            trHTML += '<tr><td>' + today + '</td><td class="emp_name">' + item.emp_code + '</td><td>' + item.emp_name + '</td><td>' + item.shift_code + '</td><td>' + item.shift_name + '</td><td>' + item.department_code + '</td><td>' + item.department_name + '</td></tr>';
                        });

                        $('#records_table').append(trHTML);
                        $("#printtable").show();
                    }
                    else{
                        alert('Data Not Found');
                    }
                    
                },
            });
        });

        $("#printtable").click(function(){
            var divToPrint=document.getElementById("records_table");
            newWin= window.open("");
            newWin.document.write('<html>');
            newWin.document.write('<head><style>');
            newWin.document.write('#records_table {font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;border-collapse: collapse;width: 100%;}');
            newWin.document.write('#records_table td, #records_table th {border: 1px solid #ddd;padding: 8px;}');
            newWin.document.write('#records_table tr:hover {background-color: #ddd;}');
            newWin.document.write('#records_table th {padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #4CAF50;color: white;}');
            newWin.document.write('</style></head>');
            newWin.document.write('<body >');
            newWin.document.write(divToPrint.outerHTML);
            newWin.document.write('</body>');
            newWin.document.write('</html>');
            newWin.print();
            newWin.close();
        });

      });
    </script>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection