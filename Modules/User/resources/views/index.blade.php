@extends('user::layouts.master')

@section('content')
    <h1>Hello World this is default template user index</h1>

    <p>Module: {!! config('user.name') !!}</p>
@endsection
