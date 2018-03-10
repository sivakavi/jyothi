@extends('admin.layouts.admin')

@section('title', __('views.admin.dashboard.title'))

@section('content')
    <div class="page-header clearfix"></div>
    <div class="margin-top-30">
        <h1>Page Under Construction</h1>
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