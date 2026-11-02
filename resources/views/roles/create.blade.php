@extends('layouts.master')

@section('title', 'Add Role')

@section('content')
    @include('roles._dark_form', ['role' => null])
@endsection
