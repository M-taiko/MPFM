@extends('layouts.master')

@section('title', 'Tower Details')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tower Details: {{ $tower->name }}</h3>
                <div class="card-options">
                    @can('update', $tower)
                    <a href="{{ route('towers.edit', $tower->id) }}" class="btn btn-primary btn-sm">
                        <i class="fe fe-edit"></i> Edit
                    </a>
                    @endcan
                    <a href="{{ route('towers.index') }}" class="btn btn-secondary btn-sm">
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
                                <td>{{ $tower->name }}</td>
                            </tr>
                            <tr>
                                <th>Code:</th>
                                <td>{{ $tower->code }}</td>
                            </tr>
                            <tr>
                                <th>Oil Field:</th>
                                <td>
                                    <a href="{{ route('oil_fields.show', $tower->oilField->id) }}">
                                        {{ $tower->oilField->name }} ({{ $tower->oilField->code }})
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($tower->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($tower->status == 'inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                    @else
                                        <span class="badge bg-warning">Maintenance</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td>{{ $tower->description ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $tower->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4>Oil Strings ({{ $tower->oilStrings->count() }})</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tower->oilStrings as $oilString)
                                    <tr>
                                        <td>{{ $oilString->name }}</td>
                                        <td>{{ $oilString->code }}</td>
                                        <td>
                                            @if($oilString->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($oilString->status == 'inactive')
                                                <span class="badge bg-secondary">Inactive</span>
                                            @else
                                                <span class="badge bg-warning">Maintenance</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('oil_strings.show', $oilString->id) }}" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No oil strings found.</td>
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

