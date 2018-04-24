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
    <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 30px;" action="{{ route('dept.importExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
    <div class="row top_tiles margin-top-40">
        <div class="col-md-3">
            <div>Bulk From: <input type="text" id="datepickerFrom" name="fromDate" required></div>
        </div>
        <div class="col-md-3">
            <div>Bulk To: <input type="text" id="datepickerTo" name="toDate" required></div>
        </div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="file" name="import_file" required/>
            @if($errors->has("import_file"))
                <span class="help-block">{{ $errors->first("import_file") }}</span>
            @endif
            <br/>
        <button class="btn btn-primary">Import File</button>
    </form>
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
      });
    </script>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection