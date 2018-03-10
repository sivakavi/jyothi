@extends('staff.layouts.staff')

@section('title', $student->name.' Dashboard')

@section('content')

<style>
.tile-stats
{
    border: 1px solid #2a3f54;
}
#box1
{
    border: 1px solid #2a3f54;

}


</style>

    <div class="page-header clearfix"></div>

    

    <div class="row top_tiles margin-top-40">
                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12" >
                    <div class="tile-stats">
                    <div class="icon"><i class="fa fa-institution"></i></div>
                    <div class="count">{{ $totalCount }}</div>
                    <h3>Lessons</h3>
                    <p>Total Lessons</p>
                    </div>
                </div>
                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="tile-stats">
                    <div class="icon"><i class="fa fa-cubes"></i></div>
                    <div class="count">{{ $viewedCount }}</div>
                    <h3>Completed</h3>
                    <p>No. of Completed Lessons.</p>
                    </div>
                </div>
               
                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="tile-stats">
                    <div class="icon"><i class="fa fa-cubes"></i></div>
                    <div class="count">{{ $totalCount - $viewedCount }}</div>
                    <h3>Pending</h3>
                    <p>No. of Pending Lessons.</p>
                    </div>
                </div>
                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="tile-stats">
                    <div class="icon"><i class="fa fa-institution"></i></div>
                    <div class="count" style="font-size:16px !important; margin-top:5px;">{{ ($student->past_last_login)?Carbon\Carbon::parse($student->past_last_login)->format('d-m-Y h:i:s'):'Never Connected' }}</div>
                    <h3 class="margin-top-30">Last Login</h3>
                    <p>Member's Last Login</p>
                    </div>
                </div>
     
    </div>  

    @if($lastViewed)
        <div class="alert alert-info row margin-top-50">{{ 'Last Viewed Lesson "'.$lastViewed->subCategoryFile->file.'" in "'.$lastViewed->subCategory->name.'" category' }}</div>
    @endif

    <div class="row margin-top-50">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Category List</small></h2>
                    <ul class="nav navbar-right">
                    <li class="cursor-pointer"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                   <div class="row margin-top-20">
                        @if(count($categories))
                                @foreach($categories as $category => $categoryName)
                                <div class="col-md-3 col-xs-12 widget widget_tally_box" >
                                    <div class="x_panel student-category">
                                    <div class="x_content">
                                        <h4 class="name"> {{ $categoryName }} </h4>
                                    </div>
                                    </div>
                                    
                                </div>
                                
                                @endforeach
                            @else
                                <p>No Category found</p>
                            @endif
                   </div>
                    

                  </div>
                </div>
              </div>
    </div>

    <div class="row margin-top-50">

        <div class="col-sm-12">
                <div class="x_panel tile">
                    <div class="x_title">
                    <h2>Lessons Status</h2>
                    <ul class="nav navbar-right">
                    <li class="cursor-pointer"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    </ul>
                    <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                        <div class="row">
                            <div class="col-md-8 col-sm-12 col-xs-12 center-margin margin-top-30">
                                @foreach($subCategoriesGroups as $subCategoriesGroup)
                                    <div class="widget_summary">
                                        <div class="w_left w_25">
                                        <span>{{ $subCategoriesGroup['name'] }}</span>
                                        </div>
                                        <div class="w_center w_55">
                                        <div class="progress">
                                            <div class="progress-bar bg-green" role="progressbar"  style="width: {{ $subCategoriesGroup['progress'] }}%;">
                                                {{ $subCategoriesGroup['progress'] }}%
                                            </div>
                                        </div>
                                        </div>
                                        <div class="w_right w_20">
                                        <span class="font-size-15">{{ $subCategoriesGroup['progress'] }}%</span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    
                    </div>
                </div>
        </div>
    </div>
    
    <br/>
    <br/>
    <br/>

@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection