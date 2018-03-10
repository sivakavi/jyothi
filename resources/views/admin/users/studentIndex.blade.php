@extends('admin.layouts.admin')
@section('content')

    <div class="page-header">
        <h1>Student List</h1>
    </div>
    @include('error')

    <div class="row">
        <div class="col-sm-12 col-xs-12 col-md-6 center-margin">

            <form name="student_list" action="{{ route('admin.users.studentList') }}" method="GET">
                <div class="form-group">
                    <label for="college_id">College</label>
                    <select id = 
                    "college_id" class="form-control" name="college_id" required>
                        <option value="">Select any one College...</option>
                        @foreach($colleges as $college)
                            <option value="{{$college->id}}">{{$college->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="group_id">Group</label>
                    <select id = "group_id" class="form-control" name="group_id" required>
                        <option value="">Select any one Group...</option>
                    </select>
                </div>
                <br/>
                <center>
                    <button type="submit" id="update" style="width:200px" class="btn btn-success">Get List</button>
                </center>
            </form>

        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $( document ).ready(function() {
            $( "#college_id" ).change(function() {
                var ajaxUrl = "{{ route('admin.assigns.getGroup') }}";
                var $select = $('#group_id');
                $select.find('option').remove();
                if($(this).val()!=""){
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        data: {
                            college_id: $(this).val()
                        },
                        success:function(response) {
                            var $select = $('#group_id');
                            $select.find('option').remove();
                            $select.append('<option value=' + '' + '>' + 'Select any one Group...' + '</option>');
                            $.each(response,function(key, value) 
                            {
                                $select.append('<option value=' + key + '>' + value + '</option>');
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection