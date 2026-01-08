@extends('layouts.master')

@section('title', 'Oil Field Details')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Oil Field Details: {{ $oilField->name }}</h3>
                <div class="card-options">
                    @can('update', $oilField)
                    <a href="{{ route('oil_fields.edit', $oilField->id) }}" class="btn btn-primary btn-sm">
                        <i class="fe fe-edit"></i> Edit
                    </a>
                    @endcan
                    <a href="{{ route('oil_fields.index') }}" class="btn btn-secondary btn-sm">
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
                                <td>{{ $oilField->name }}</td>
                            </tr>
                            <tr>
                                <th>Code:</th>
                                <td>{{ $oilField->code }}</td>
                            </tr>
                            <tr>
                                <th>Location:</th>
                                <td>{{ $oilField->location ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($oilField->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($oilField->status == 'inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                    @else
                                        <span class="badge bg-warning">Maintenance</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td>{{ $oilField->description ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $oilField->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4>Towers ({{ $oilField->towers->count() }})</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Status</th>
                                        <th>Oil Strings</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($oilField->towers as $tower)
                                    <tr>
                                        <td>{{ $tower->name }}</td>
                                        <td>{{ $tower->code }}</td>
                                        <td>
                                            @if($tower->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($tower->status == 'inactive')
                                                <span class="badge bg-secondary">Inactive</span>
                                            @else
                                                <span class="badge bg-warning">Maintenance</span>
                                            @endif
                                        </td>
                                        <td>{{ $tower->oilStrings->count() }}</td>
                                        <td>
                                            <a href="{{ route('towers.show', $tower->id) }}" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No towers found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

