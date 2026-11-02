@extends('layouts.master')

@section('title', 'Add Test Vessel')

@section('content')
    @include('test_vessels._dark_form', ['testVessel' => null])
@endsection
