@extends('layouts.master')

@section('title', 'Edit User')

@section('content')
    @include('users._dark_form', ['user' => $user])
@endsection
