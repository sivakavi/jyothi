@extends('admin.layouts.admin')

@section('title', 'Category / Create')

@section('content')
    <div class="page-header clearfix"></div>

    @include('error')

    <div class="row margin-top-30">
        <div class="col-md-8 center-margin">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Category Create Form</h2>
                                    <ul class="nav navbar-right">
                                    <li class="cursor-pointer"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content margin-top-40">
                                    <div class="form-group @if($errors->has('name')) has-error @endif">
                                        <label for="name-field">Category Name</label>
                                        <input type="text" id="name-field" name="name" class="form-control" value="{{ old("name") }}"/>
                                        @if($errors->has("name"))
                                            <span class="help-block">{{ $errors->first("name") }}</span>
                                        @endif
                                    </div>

                                    <div class="well well-sm margin-top-50">
                                        <button type="submit" class="btn btn-primary btn-round btn-sm">Create Category</button>
                                        <a class="btn btn-link pull-right" href="{{ route('admin.categories.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    
            </form>
        </div>
    </div>

@endsection
