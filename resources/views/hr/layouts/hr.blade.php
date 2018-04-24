@extends('layouts.app')

@section('body_class','nav-md')

@section('page')
    <div class="container body">
        <div class="main_container">
            @section('header')
                @include('hr.sections.navigation')
                @include('hr.sections.header')
            @show

            @yield('left-sidebar')

            <div class="right_col" role="main">
                <div class="page-title">
                    <!-- <div class="title_left"> -->
                    <div>
                        <center><h1 class="h3">@yield('title')</h1></center>
                    </div>
                </div>
                <div>
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
                @include('hr.sections.footer')
            </footer>
        </div>
    </div>
@stop

@section('styles')
    {{ Html::style(mix('assets/admin/css/admin.css')) }}
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
    {{ Html::style('css/jquery-ui.css') }}
@endsection

@section('scripts')
    {{ Html::script(mix('assets/admin/js/admin.js')) }}
    {{ Html::script('js/jquery-ui.js') }}
    {{ Html::script('js/jquery.table2excel.min.js') }}
@endsection