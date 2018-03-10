@extends('student.layouts.student')

@section('title', 'Lessons List')

@section('content')
    <div class="page-header clearfix"></div>
    
    <div class="row margin-top-40">

    @if(count($subCategories))
        @foreach($subCategories as $key => $subCategory)
        <div class="col-md-3 col-xs-12 widget widget_tally_box max-width-none">
            <div class="x_panel ui-ribbon-container">
                <!-- <div class="ui-ribbon-wrapper">
                    <div class="ui-ribbon">
                    30% Off
                    </div>
                </div> -->
                <div class="x_title">
                    <h2 class="white-space">{{ $subCategory['name'] }}</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-6">
                        <div style="margin-bottom: 17px">
                                <div class="c100 p{{ $subCategory['progress'] }}">
                                <span>{{ $subCategory['progress'] }}%</span>
                                <div class="slice">
                                    <div class="bar"></div>
                                    <div class="fill"></div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                        <p>{{ $subCategory['category_name'] }}</p>
                        <div class="divider"></div>
                        <center>
                            <a href="{{ route('student.sub.category', $subCategory['id']) }}">
                                <button type="button" class="btn btn-round btn-success">Detail View</button>
                            </a>
                        </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <p>No Sub Category found</p>
    @endif

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