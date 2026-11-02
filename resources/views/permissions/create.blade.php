@extends('layouts.master')

@section('title', 'Add Permission')

@section('content')
    @include('permissions._dark_form', ['permission' => null])
@endsection
