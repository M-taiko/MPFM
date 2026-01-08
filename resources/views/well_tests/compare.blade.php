@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <h3>Compare Well Tests</h3>

    <form method="POST" action="{{ route('well_tests.compare.submit') }}">
        @csrf

        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Select Well Tests to Compare</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse(\App\Models\WellTest::latest()->get() as $test)
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="test_ids[]" value="{{ $test->id }}" id="test_{{ $test->id }}">
                                        <label class="form-check-label" for="test_{{ $test->id }}">
                                            <strong>{{ $test->well }}</strong><br>
                                            <small>
                                                Client: {{ $test->client }}<br>
                                                Field: {{ $test->field }}<br>
                                                Date: {{ $test->created_at->format('Y-m-d H:i') }}
                                            </small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p>No well tests available for comparison.</p>
                        </div>
                    @endforelse
                </div>
                
                @if(\App\Models\WellTest::count() > 0)
                <button class="btn btn-primary" type="submit">
                    <i class="fe fe-bar-chart me-2"></i>Compare Selected Tests
                </button>
                @endif
            </div>
        </div>
    </form>

</div>
@endsection