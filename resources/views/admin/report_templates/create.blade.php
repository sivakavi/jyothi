@extends('admin.layouts.admin')

@section('title', 'Report Template Create Form')

<style>
    .box-container {
        height: 250px;
        overflow-y: scroll;
    }

    .box-item {
        z-index: 1000
    }

</style>

@section('content')

   <div class="page-header clearfix"></div>

    @include('error')

    <div class="row margin-top-30">
        <div class="col-md-12 center-margin">
            <form class="form-horizontal form-label-left" action="" method="POST">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Report Template Create Form</h2>
                                    <ul class="nav navbar-right">
                                    <li class="cursor-pointer"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content margin-top-40">
                                <br/>
                                    <div class="form-group @if($errors->has('name')) has-error @endif">
                                    <label for="name-field">Name</label>
                                    <input type="text" id="tempName" name="name" class="form-control" value="{{ old("name") }}"/>
                                    @if($errors->has("name"))
                                        <span class="help-block">{{ $errors->first("name") }}</span>
                                    @endif
                                    </div>
                                    <br/>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div>
                                                <div class="panel panel-default column-list">
                                                    <div class="panel-heading">
                                                    <h1 class="panel-title">Column List</h1>
                                                    </div>
                                                    <div id="container1" class="panel-body box-container">
                                                        <div itemid="work_dept_name" class="btn btn-default box-item">Work Department Name</div>
                                                        <div itemid="work_dept_code" class="btn btn-default box-item">Work Department Code</div>
                                                        <div itemid="shift_name" class="btn btn-default box-item">Shift Name</div>
                                                        <div itemid="shift_code" class="btn btn-default box-item">Shift Code</div>
                                                        <div itemid="shift_date" class="btn btn-default box-item">Shift Date</div>
                                                        <div itemid="status" class="btn btn-default box-item">Status</div>
                                                        <div itemid="process" class="btn btn-default box-item">Process</div>
                                                        <div itemid="ot_hours" class="btn btn-default box-item">OT Hours</div>
                                                        <div itemid="ot_department" class="btn btn-default box-item">OT Department</div>
                                                        <div itemid="emp_name" class="btn btn-default box-item">Emp. Name</div>
                                                        <div itemid="emp_dept_name" class="btn btn-default box-item">Emp. Department Name</div>
                                                        <div itemid="emp_dep_code" class="btn btn-default box-item">Emp. Department Code</div>
                                                        <div itemid="emp_code" class="btn btn-default box-item">Emp. Code</div>
                                                        <div itemid="cost_centre" class="btn btn-default box-item">Cost Centre</div>
                                                        <div itemid="cost_centre_desc" class="btn btn-default box-item">Cost Centre Desc</div>
                                                        <div itemid="gl_account" class="btn btn-default box-item">GL Account</div>
                                                        <div itemid="gl_account_desc" class="btn btn-default box-item">GL Account Desc</div>
                                                        <div itemid="location" class="btn btn-default box-item">Emp. Location</div>
                                                        <div itemid="category" class="btn btn-default box-item">Emp. Category Name</div>
                                                        <div itemid="gender" class="btn btn-default box-item">Emp. Gender</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div>
                                                <div class="panel panel-default report-column-list">
                                                    <div class="panel-heading">
                                                        <h1 class="panel-title">Report Column List</h1>
                                                        </div>
                                                        <div id="container2" class="panel-body box-container"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="well well-sm margin-top-50">
                                        <button type="button" id="saveBtn" class="btn btn-primary btn-round btn-sm">Create Report Template</button>
                                        <a class="btn btn-link pull-right" href="{{ route('admin.report_templates.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
            </form>
        </div>
    </div>
    
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    <script>

    $('.box-item').draggable({
            cursor: 'move',
            helper: "clone"
        });

    $("#container1").droppable({
            drop: function(event, ui) {
            var itemid = $(event.originalEvent.toElement).attr("itemid");
            $('.box-item').each(function() {
                if ($(this).attr("itemid") === itemid) {
                $(this).appendTo("#container1");
                }
            });
            }
        });

        $("#container2").droppable({
            drop: function(event, ui) {
            var itemid = $(event.originalEvent.toElement).attr("itemid");
            $('.box-item').each(function() {
                if ($(this).attr("itemid") === itemid) {
                $(this).appendTo("#container2");
                }
            });
            }
        });

    $("#saveBtn").click(function() {
        $(this).click(function() {
            return false;
        });
        return true;
    });

    $('#saveBtn').click(function(e){

            var field_array = [];
            $("#container2 div").each(function() {
                field_array.push($(this).attr("itemid"));
            });

            var headerData = [];
            field_array.forEach(function(item, index){
                headerData.push(item.toUpperCase());
            });
            
            var templateName = $("#tempName").val();
            var frontend_data = headerData.toString();
            var backend_data = field_array.toString();
        
        var templateData = {
            name: templateName,
            frontend_data: frontend_data,
            backend_data: backend_data
        };

        if(templateName){
            if(field_array.length){
                e.stopPropagation();
                jQuery.ajax({
                  url: "{{route('admin.saveTemplate')}}",
                  type: 'POST',
                  data: {
                      'templateData' : templateData,
                      '_token' : $( "#token" ).val()
                  },
                  success:function(data) {
                      alert('Report Template Created Successfully');
                      location.href = "{{ route('admin.report_templates.index') }}";
                  },
                });
            }else{
                alert("Please choose some report column list value...");
            }
        }else{
            alert("please enter template name...");
        }

        
        

    });

    </script>

@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection
