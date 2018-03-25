@extends('dept.layouts.dept')

@section('title', 'Shift List')
<style>
    .box-style{
        border: 1px solid;
        padding: 20px;
        text-align: center;
        margin-bottom: 15px;
    }
</style>
@section('content')
    <div class="page-header clearfix">
    </div>
    <div class="row" style="margin-top:80px;">
        <div class="col-md-12">
            @foreach ($shiftDetails as $shiftDetail)
                <a href="{{ URL::route('dept.shiftDetails', array('shift_id'=> $shiftDetail['id'],'date'=> $shiftDetail['date'])) }}">
                <div class="col-md-6 box-style">
                    {{ $shiftDetail['date'].'-'.$shiftDetail['allias'].'-'.$shiftDetail['name'] }}
                </div></a>
            @endforeach
        </div>
    </div>

@endsection