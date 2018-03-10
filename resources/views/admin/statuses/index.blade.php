@extends('admin.layouts.admin')

@section('title', 'Status List')

@section('content')
    <div class="page-header clearfix">
    </div>
    <h1>
        <a class="btn btn-success pull-right" href="{{ route('admin.statuses.create') }}">
            <i class="glyphicon glyphicon-plus"></i> Create
        </a>
    </h1>
    <div class="row" style="margin-top:80px;">
        <div class="col-md-12">
            @if($statuses->count())
                <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%">
                    <thead>
                        <tr>
                        <th>ID</th>
                        <th>NAME</th>
                        <th>DEPARTMENT</th>
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($statuses as $status)
                            <tr>
                                <td>{{$status->id}}</td>
                                <td>{{$status->name}}</td>
                                <td>{{$status->department->name}}</td>
                                <td class="text-right">
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.statuses.show', $status->id) }}"><i class="glyphicon glyphicon-eye-open"></i> View</a>
                                    <a class="btn btn-xs btn-warning" href="{{ route('admin.statuses.edit', $status->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                    <form action="{{ route('admin.statuses.destroy', $status->id) }}" method="POST" style="display: inline;" onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pull-right">
                    {{ $statuses->links() }}
                </div>
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif

        </div>
    </div>

@endsection