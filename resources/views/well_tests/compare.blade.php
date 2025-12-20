@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h3>Compare Well Tests</h3>

    <form method="POST" action="{{ route('well_tests.compare') }}">
        @csrf

        <div class="row">
            @foreach(\App\Models\WellTest::latest()->get() as $test)
                <div class="col-md-3">
                    <label>
                        <input type="checkbox" name="test_ids[]" value="{{ $test->id }}">
                        {{ $test->well }} - {{ $test->client }}
                    </label>
                </div>
            @endforeach
        </div>

        <button class="btn btn-primary mt-3">Compare</button>
    </form>

</div>
@endsection
