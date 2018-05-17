@extends('admin.layouts.admin')

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

    
    <div class="margin-top-40">
    <div style="border-bottom: 1px solid black;"></div>
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
    <br>
    <br>
    <br>
    <br>
    <a class="btn btn-link" href="{{ route('admin.checkShiftData') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
</div>

@endsection