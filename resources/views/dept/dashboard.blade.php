@extends('dept.layouts.dept')

@section('title', __('views.admin.dashboard.title'))
<style>
    .box-style{
        border: 1px solid;
        text-align: center;
        margin-bottom: 15px;
    }
</style>
@section('content')

    <div class="page-header clearfix"></div>
    <div class="row" style="margin-top:80px;">
            @foreach ($shiftDetails as $shiftDetail)
                <a href="{{ URL::route('dept.shiftDetails', array('shift_id'=> $shiftDetail['id'],'date'=> $shiftDetail['date'])) }}">
                    <div class="col-md-6" style="padding:10px !important;">
                        <div class="box-style">
                            {{ $shiftDetail['date'].'-'.$shiftDetail['allias'].'-'.$shiftDetail['name'] }}
                        </div>
                    </div>
                </a>
            @endforeach
    </div>
    

@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection