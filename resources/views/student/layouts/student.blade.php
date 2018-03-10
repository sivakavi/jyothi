@extends('layouts.app')

@section('body_class','nav-md')

@section('page')
    <div class="container body">
        <div class="main_container">
            @section('header')
                @include('student.sections.navigation')
                @include('student.sections.header')
            @show

            @yield('left-sidebar')

            <div class="right_col" role="main">
                <div class="page-title">
                    <div class="title_left">
                        <h1 class="h3">@yield('title')</h1>
                    </div>
                    @if(Breadcrumbs::exists())
                        <div class="title_right">
                            <div class="pull-right">
                                {!! Breadcrumbs::render() !!}
                            </div>
                        </div>
                    @endif
                </div>
                @yield('content')
            </div>

            <footer>
                @include('student.sections.footer')
            </footer>
        </div>
    </div>
@stop

@section('styles')
    {{ Html::style(mix('assets/admin/css/admin.css')) }}
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection

@section('scripts')
    {{ Html::script(mix('assets/admin/js/admin.js')) }}
@endsection