@extends('hr.layouts.hr')

@section('title', ' Shift Punch Records Compare')

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
    <div style="margin-top:20px;">
    <a href={{ asset('assets/demo/DemoPunch.xlsx') }}><button class="btn btn-success" style="float:right;">Download Sample File</button></a>
    <br/>
    <br/>
    <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 30px;" action="{{ route('hr.importExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
        <div class="row top_tiles margin-top-40">
            <div class="col-md-4">
                <div>Punch From: <input type="text" id="datepickerFrom" name="fromDate" required></div>
            </div>
            <div class="col-md-4">
                <div>Punch To: <input type="text" id="datepickerTo" name="toDate" required></div>
            </div>
            <div class="col-md-4">
                <div><input type="file" name="import_file" required style="display: inline"/></div>
                @if($errors->has("import_file"))
                    <span class="help-block">{{ $errors->first("import_file") }}</span>
                @endif
            </div>
        </div>
        <div class="row text-center margin-top-40">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <button class="btn btn-primary">Import File</button>
        </div>
    </form>
    </div>

    <div class="margin-top-40">
    @if(isset($missingDatas) && isset($differDatas))
    <h4> Result </h4>
        @if(count($missingDatas) || count($differDatas))
            @if(count($missingDatas))
                <br/>
                <div>
                Missing Employee Codes are :
                <br/>
                <br/>
                 {{implode(', ', $missingDatas)}}  
                </div>
            @endif
            @if(count($differDatas))
                <br/>
                <br/>
                <div>
                Differ Serial no are :
                <br/>
                <br/>
                 {{implode(', ', $differDatas)}}  
                </div>
            @endif
        @else
            <div>
                All Records correct  
            </div>
        @endif
    @endif
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
      });
    </script>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection