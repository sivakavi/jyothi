@extends('dept.layouts.dept')

@section('title', 'Shift Status List')

@section('content')
    <div class="page-header clearfix">
    </div>
    <div class="row" style="margin-top:80px;">
        <div class="col-md-12">
            @if($batches->count())
                <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%">
                    <thead>
                        <tr>
                        <th>ID</th>
                        <th>FROM DATE</th>
                        <th>TO DATE</th>
                        <th>EMPLOYEE</th>
                        <th>EMPLOYEE ID</th>
                        <th>EMPLOYEE DEPARTMENT</th>
                        <th>STATUS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($batches as $batch)
                            <tr>
                                <td>{{$batch->id}}</td>
                                <td>{{$batch->fromDate}}</td>
                                <td>{{$batch->toDate}}</td>
                                <td>{{$batch->employee->name}}</td>
                                <td>{{$batch->employee->employee_id}}</td>
                                <td>{{$batch->employee->department->name}}</td>
                                <td>{{$batch->status}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pull-right">
                    {{ $batches->links() }}
                </div>
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif

        </div>
    </div>

@endsection