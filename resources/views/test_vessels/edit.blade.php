@extends('layouts.master')

@section('title', 'Edit Test Vessel')

@section('content')
    @include('test_vessels._dark_form', ['testVessel' => $testVessel])
@endsection
