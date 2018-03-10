@extends('student.layouts.student')

@section('title', 'Lesson Detail')

@section('content')
<div class="page-header clearfix"></div>
   
<div class="row margin-top-30">
    <div class="col-md-8 col-sm-10 col-xs-12 center-margin">
    <div class="x_panel">
      <div class="x_title">
        <h2>{{ $subCategory['name'] }}</h2>
        <ul class="nav navbar-right">
        <li class="cursor-pointer"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
        </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div class="margin-top-40" role="tabpanel" data-example-id="togglable-tabs">
          <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist" style="background: inherit !important;">
            <li role="presentation" class="active">
                <a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">
                &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; Lessons &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </a>
            </li>
            <li role="presentation" class="">
                <a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Tests &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </a>
            </li>
          </ul>
          <div id="myTabContent" class="tab-content">
            <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                @foreach($subCategoryFiles as $subCategoryFileId => $subCategoryFileName)
                <a href="{{ route('student.view.pdf', $subCategoryFileId) }}"><button type="button" class="btn btn-success margin-top-20">{{ $subCategoryFileName }}</button></a>
                <br/>
                @endforeach
            </div>
            <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
                @foreach($test as $testId => $testName)
                <a href="{{ route('student.test', $testId, $subCategory['id']) }}?subCatId={{ $subCategory['id'] }}"><button type="button" class="btn btn-warning margin-top-20">{{ $testName }}</button></a>
                <br/>
                @endforeach
            </div>
          </div>
        </div>
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