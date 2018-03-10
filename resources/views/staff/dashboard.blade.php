@extends('staff.layouts.staff')

@section('title', __('views.admin.dashboard.title'))

@section('content')

    <div class="page-header clearfix"></div>

    

    <div class="row top_tiles margin-top-40">
                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12" >
                    <div class="tile-stats">
                    <div class="icon"><i class="fa fa-users"></i></div>
                    <div class="count">{{ $userCount }}</div>
                    <h3 class="margin-top-20">No. of Users</h3>
                    </div>
                </div>
                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="tile-stats">
                    <div class="icon"><i class="fa fa-cubes"></i></div>
                    <div class="count">{{ $groupCount }}</div>
                    <h3 class="margin-top-20">No. of Groups</h3>
                    </div>
                </div>
                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <a target="_blank" href="{{ route('staff.studentLists')}}?active=true">
                        <div class="tile-stats">
                        <div class="icon"><i class="fa fa-users"></i></div>
                        <div class="count">{{ $activeUserCount }}</div>
                        <h3 class="margin-top-20">No. of Active Users</h3>
                        </div>
                    </a>
                </div>
                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <a target="_blank" href="{{ route('staff.studentLists')}}?inactive=true">
                        <div class="tile-stats">
                        <div class="icon"><i class="fa fa-users"></i></div>
                        <div class="count">{{ $userCount-$activeUserCount }}</div>
                        <h3 class="margin-top-20">No. of Inactive Users</h3>
                        </div>
                    </a>
                </div>
                
    </div>  
    <div class="divider"></div>
    <br/>
    <br/>
    <br/>
    <div class="row margin-top-50">
        <div class="form-group col-md-6 col-sm-12 col-xs-12">
            <label for="group_id">Filter Student by Group</label>
                <select id = "group_id" class="form-control" name="group_id" required>
                    <option value="">Select Any Group</option>
                    @foreach($groups as $group)
                    <option value="{{$group->id}}">{{$group->name}}</option>
                    @endforeach
                </select>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    <script>
        $(function(){
            $('#group_id').change(function(e){
                e.preventDefault();
                if($(this).val()){
                    var url = "{{ route('staff.studentLists')}}";
                    window.open(
                    url+"?group_id="+$(this).val(),
                    '_blank' // <- This is what makes it open in a new window.
                    );
                }
            })
        })
    </script>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection