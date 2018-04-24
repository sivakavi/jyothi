@extends('hr.layouts.hr')

@section('title', 'Report')

<style>
    .box-container {
        height: 250px;
        overflow-y: scroll;
    }

    .box-item {
        z-index: 1000
    }

    /* .report-column-list .box-item {
        width: 100%;
        z-index: 1000
    } */

    /* #repotTable th td {
        border: 1px solid black;
        padding:10px !important;
    }

    #repotTable tr td {
        border: 1px solid black;
        padding:10px !important;
    } */

    .reaport-area{
        display: none;
    }

    .table-area{
        height:500px;
        width:100%;
        overflow-x:scroll;
        overflow-y:scroll;
    }

    .flip, .flip #repotTable{
        transform:rotateX(180deg);
        -ms-transform:rotateX(180deg); /* IE 9 */
        -webkit-transform:rotateX(180deg); /* Safari and Chrome */
    }

    

</style>

@section('content')
    <div class="page-header clearfix">
    </div>
    <div class="row" style="margin-top:40px;">
        <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div>From : <input type="text" id="datepickerFrom"></div>
            </div>
            <div class="col-md-6">
                <div>To : <input type="text" id="datepickerTo"></div>
            </div>
        </div>
        <br/>
        <div class="row">
                <div class="col-md-6">
                    <div>
                        <div class="panel panel-default column-list">
                            <div class="panel-heading">
                            <h1 class="panel-title">Column List</h1>
                            </div>
                            <div id="container1" class="panel-body box-container">
                                <div itemid="work_dept_name" class="btn btn-default box-item">Work Department Name</div>
                                <div itemid="work_dept_code" class="btn btn-default box-item">Work Department Code</div>
                                <div itemid="shift_name" class="btn btn-default box-item">Shift Name</div>
                                <div itemid="shift_code" class="btn btn-default box-item">Shift Code</div>
                                <div itemid="shift_date" class="btn btn-default box-item">Shift Date</div>
                                <div itemid="status" class="btn btn-default box-item">Status</div>
                                <div itemid="process" class="btn btn-default box-item">Process</div>
                                <div itemid="leave_type" class="btn btn-default box-item">Leave Type</div>
                                <div itemid="ot_hours" class="btn btn-default box-item">OT Hours</div>
                                <div itemid="emp_name" class="btn btn-default box-item">Emp. Name</div>
                                <div itemid="emp_dept_name" class="btn btn-default box-item">Emp. Department Name</div>
                                <div itemid="emp_dep_code" class="btn btn-default box-item">Emp. Department Code</div>
                                <div itemid="emp_code" class="btn btn-default box-item">Emp. Code</div>
                                <div itemid="cost_centre" class="btn btn-default box-item">Cost Centre</div>
                                <div itemid="cost_centre_desc" class="btn btn-default box-item">Cost Centre Desc</div>
                                <div itemid="gl_account" class="btn btn-default box-item">GL Account</div>
                                <div itemid="gl_account_desc" class="btn btn-default box-item">GL Account Desc</div>
                                <div itemid="location" class="btn btn-default box-item">Emp. Location</div>
                                <div itemid="category" class="btn btn-default box-item">Emp. Category Name</div>
                                <div itemid="gender" class="btn btn-default box-item">Emp. Gender</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <div class="panel panel-default report-column-list">
                            <div class="panel-heading">
                            <h1 class="panel-title">Report Column List</h1>
                            </div>
                            <div id="container2" class="panel-body box-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-md-12">
            <center>
            <button id="reportBtn" class="btn btn-primary btn-round btn-sm">Get Report</button>
            <center>
            </div>
        </div>
        <br/>
        <div class="row reaport-area">
            <div class="col-md-12 table-area">
                <table id="repotTable" class="table table-bordered" >
                    <thead id="reportTableHead">
                    </thead>
                    <tbody id="reportTableBody">
                    </tbody>
                </table>
            </div>
        </div>
        <br/>
        <div class="row reaport-area">
            <div class="col-md-12">
            <center>
            <button id="exportBtn" class="btn btn-primary btn-round btn-sm">Export Report</button>
            <center>
            </div>
        </div>
        <br/>
    </div>

@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    <script type="text/javascript">

        var xlfilename = "sample";

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

        $('.box-item').draggable({
            cursor: 'move',
            helper: "clone"
        });

        $("#datepickerFrom").datepicker({
            dateFormat: 'dd/mm/yy',
            maxDate: new Date(),
            onSelect: function(dateText, inst) {
                var fromdate = $(this).val();
                $( "#datepickerTo" ).val('');
            }
        });

        $("#datepickerTo").datepicker({
            dateFormat: 'dd/mm/yy',
            maxDate: new Date(),
            onSelect: function(dateText, inst) {
                var todate = $(this).val();
                fromDate = dateConversion($("#datepickerFrom"));
                toDate = dateConversion($(this));
                if(fromDate>toDate){
                  alert('From Date must less than To date');
                  $( "#datepickerTo" ).val('');
                  return;
                }
            }
        });

        $("#container1").droppable({
            drop: function(event, ui) {
            var itemid = $(event.originalEvent.toElement).attr("itemid");
            $('.box-item').each(function() {
                if ($(this).attr("itemid") === itemid) {
                $(this).appendTo("#container1");
                }
            });
            }
        });

        $("#container2").droppable({
            drop: function(event, ui) {
            var itemid = $(event.originalEvent.toElement).attr("itemid");
            $('.box-item').each(function() {
                if ($(this).attr("itemid") === itemid) {
                $(this).appendTo("#container2");
                }
            });
            }
        });

        $("#reportBtn").click(function(){
            var field_array = [];
            $("#container2 div").each(function() {
                field_array.push($(this).attr("itemid"));
            });

            var fromDate = $("#datepickerFrom").val();
            var toDate = $("#datepickerTo").val();

            var fromD = fromDate.split("/");
            var toD = toDate.split("/");
            var finalFrom = fromD[2] + "-" + fromD[1] + "-" + fromD[0];
            var finalTo = toD[2] + "-" + toD[1] + "-" + toD[0];

            var fromDate1 = new Date(finalFrom);
            var day = fromDate1.getDate();
            var month = fromDate1.getMonth() + 1;
            var year = fromDate1.getFullYear();
            var fd = day + "-" + month + "-" + year;

            var toDate1 = new Date(finalTo);
            var day1 = toDate1.getDate();
            var month1 = toDate1.getMonth() + 1;
            var year1 = toDate1.getFullYear();
            var td = day1 + "-" + month1 + "-" + year1;

            xlfilename = fd + "__" + td + "__full__report";

            var headerData = [];
            field_array.forEach(function(item, index){
                headerData.push(item.toUpperCase());
            });
            
            if(fromDate && toDate){
                if(field_array.length){
                    var datas = 'fromDate='+ fromDate + '&toDate='+ toDate + '&fieldArray='+ field_array;
                    jQuery.ajax({
                        url: "{{route('hr.getReport')}}",
                        type: 'GET',
                        data: datas,
                        success:function(data) {
                            $("#reportTableHead").empty();
                            $("#reportTableBody").empty();
                            var theadContent = '';
                            headerData.forEach(function(item, index){
                                theadContent = theadContent+'<th>'+item+'</th>';
                            });
                            $("#repotTable #reportTableHead").append('<tr>'+theadContent+'</tr>'); 
                            for(var i=0;i<data.length;i++){
                                var tbodyContent = '';
                                field_array.forEach(function(item, index){
                                    tbodyContent = tbodyContent+'<td>'+data[i][item]+'</td>';
                                });
                                $("#repotTable #reportTableBody").append('<tr>'+tbodyContent+'</tr>'); 
                            }

                            $(".reaport-area").show();
                        },
                    });
                }else{
                    alert("Please choose some report column list value...");
                }
            }else{
                alert("Please choose date...");
            }
        });

        $("#exportBtn").click(function () {
            $("#repotTable").table2excel({
                filename: xlfilename +".xls"
            });
        });

    </script>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection