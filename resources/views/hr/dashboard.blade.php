@extends('hr.layouts.hr')

@section('title', __('views.admin.dashboard.title'))

@section('content')

    <div class="page-header clearfix"></div>

    <div style="margin-top:80px;">
        <div class="row">
            <div class="animated flipInY col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="tile-stats" style="padding:20px 0px !important;">
                    <div class="icon" style="top:45px !important;right:80px !important;"><i class="fa fa-cubes" style="font-size:80px !important;"></i></div>
                    <div class="count">{{$shifts}}</div>
                    <h3>Pending Shift Count</h3>
                </div>
            </div>
            <div class="animated flipInY col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="tile-stats" style="padding:20px 0px !important;">
                    <div class="icon" style="top:45px !important;right:80px !important;"><i class="fa fa-institution" style="font-size:80px !important;"></i></div>
                    <div class="count">{{$departments}}</div>
                    <h3>Departments</h3>
                </div>
            </div>
        </div>
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