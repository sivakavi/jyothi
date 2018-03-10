@extends('staff.layouts.staff')


@section('title', 'Profile')

@section('content')
<div class="page-header clearfix"></div>
    <div class="margin-top-50">
        <table class="table table-bordered">
            <tbody>
                <tr>
                <th scope="row">Name</th>
                <td>{{$user->name}}</td>
                </tr>
                <tr>
                <th scope="row">College Name</th>
                <td>{{$user->college->name}}</td>
                </tr>
                <tr>
                <th scope="row">User Email</th>
                <td>{{$user->email}}</td>
                </tr>
                <tr>
                <th scope="row">College Address</th>
                <td>{{$user->college->address}}</td>
                </tr>
                
            </tbody>
        </table>
    </div>

@endsection