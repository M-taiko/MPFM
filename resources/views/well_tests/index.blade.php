@extends('layouts.master')

@section('content')
<div class="container-fluid">

<h2 class="mb-4">Well Tests</h2>

<div class="mb-3">
    <button id="compareBtn" class="btn btn-primary" disabled>Compare Selected</button>
</div>

<table class="table table-bordered table-striped" id="wellTestsTable">
    <thead>
        <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>Display Name</th>
            <th>Field</th>
            <th>Client</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#wellTestsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("well_tests.index") }}',
        columns: [
            { data: 'id', render: function(data,type,row){
                return `<input type="checkbox" class="selectTest" value="${data}">`;
            }, orderable:false, searchable:false },
            { data: 'display_name', name: 'display_name' },
            { data: 'field', name: 'field' },
            { data: 'client', name: 'client' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable:false, searchable:false }
        ]
    });

    // زر select all
    $('#selectAll').on('click', function() {
        $('.selectTest').prop('checked', this.checked);
        toggleCompareButton();
    });

    // toggle زر Compare
    $(document).on('change', '.selectTest', function() {
        toggleCompareButton();
    });

    function toggleCompareButton() {
        const selected = $('.selectTest:checked').length;
        $('#compareBtn').prop('disabled', selected < 2);
    }

    // عند الضغط على Compare
    $('#compareBtn').on('click', function() {
        const selectedIds = $('.selectTest:checked').map(function(){ return this.value; }).get();
        if(selectedIds.length < 2){
            alert("Please select at least two tests to compare.");
            return;
        }

        // إرسال form لصفحة compare
        const form = $('<form>', { action: '{{ route("well_tests.compare") }}', method: 'POST' });
        form.append('@csrf');
        selectedIds.forEach(id => {
            form.append($('<input>', { type: 'hidden', name: 'test_ids[]', value: id }));
        });
        $('body').append(form);
        form.submit();
    });
});
</script>
@endpush
