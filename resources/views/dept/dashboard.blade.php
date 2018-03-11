@extends('dept.layouts.dept')

@section('title', __('views.admin.dashboard.title'))

@section('content')

    <div class="page-header clearfix"></div>
    <div>Page Under Construction</div>

@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection