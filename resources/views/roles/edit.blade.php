@extends('layouts.master')

@section('title', 'Edit Role')

@section('content')
    @include('roles._dark_form', ['role' => $role])
@endsection
