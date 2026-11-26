@extends('layouts.master')

@section('title', 'Add Testing Unit')

@section('content')
    @include('test_vessels._dark_form', ['testVessel' => null])
@endsection
