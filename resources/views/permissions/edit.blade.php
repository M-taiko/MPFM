@extends('layouts.master')

@section('title', 'Edit Permission')

@section('content')
    @include('permissions._dark_form', ['permission' => $permission])
@endsection
