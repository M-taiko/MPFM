@extends('layouts.master')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Well Test Archive</h2>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Saved Well Tests</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Field</th>
                            <th>Well</th>
                            <th>Test Duration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wellTests as $test)
                        <tr>
                            <td>{{ $test->created_at->format('Y-m-d') }}</td>
                            <td>{{ $test->client }}</td>
                            <td>{{ $test->field }}</td>
                            <td>{{ $test->well }}</td>
                            <td>
                                @if($test->start_time && $test->end_time)
                                    {{ $test->start_time->diffInHours($test->end_time) }} hours
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('well_tests.show', $test->id) }}" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $wellTests->links() }}
        </div>
    </div>
</div>
@endsection
