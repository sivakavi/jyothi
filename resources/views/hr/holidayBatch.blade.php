@extends('hr.layouts.hr')

@section('title', 'Holiday Shift Status List')
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
            @foreach ($batches as $batch)
            @if($holiday_department_count[$batch->department->id])
                <a href="{{ URL::route('hr.holidayShift', array('department_id'=> $batch->department_id)) }}">
                    <div class="col-md-6 box-style">
                        {{ $batch->department->name." - ".$holiday_department_count[$batch->department->id] }} 
                    </div></a>
                @endif
            @endforeach
            <div class="pull-right">
                {{ $batches->links() }}
            </div>
        </div>
    </div>

@endsection