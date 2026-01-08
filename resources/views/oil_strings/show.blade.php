@extends('layouts.master')

@section('title', 'Oil String Details')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Oil String Details: {{ $oilString->name }}</h3>
                <div class="card-options">
                    @can('update', $oilString)
                    <a href="{{ route('oil_strings.edit', $oilString->id) }}" class="btn btn-primary btn-sm">
                        <i class="fe fe-edit"></i> Edit
                    </a>
                    @endcan
                    <a href="{{ route('oil_strings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Name:</th>
                                <td>{{ $oilString->name }}</td>
                            </tr>
                            <tr>
                                <th>Code:</th>
                                <td>{{ $oilString->code }}</td>
                            </tr>
                            <tr>
                                <th>Tower:</th>
                                <td>
                                    <a href="{{ route('towers.show', $oilString->tower->id) }}">
                                        {{ $oilString->tower->name }} ({{ $oilString->tower->code }})
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Oil Field:</th>
                                <td>
                                    <a href="{{ route('oil_fields.show', $oilString->tower->oilField->id) }}">
                                        {{ $oilString->tower->oilField->name }} ({{ $oilString->tower->oilField->code }})
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($oilString->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($oilString->status == 'inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                    @else
                                        <span class="badge bg-warning">Maintenance</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td>{{ $oilString->description ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $oilString->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

