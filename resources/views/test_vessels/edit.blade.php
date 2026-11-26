@extends('layouts.master')

@section('title', 'Edit Testing Unit')

@section('content')
    @include('test_vessels._dark_form', ['testVessel' => $testVessel])
@endsection
