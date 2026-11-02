@extends('layouts.master')

@section('title', 'Add User')

@section('content')
    @include('users._dark_form', ['user' => null])
@endsection
