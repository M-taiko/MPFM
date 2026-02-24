<div class="assign-task-form">
    <div class="mb-3">
        <p class="text-muted"><strong>Task:</strong> {{ $task->well_full_name ?? $task->well_number }}</p>
        <p class="text-muted"><strong>Monthly Plan:</strong> {{ $task->monthlyPlan->plan_name ?? 'N/A' }}</p>
    </div>

    <div class="mb-3">
        <label for="departmentSelect" class="form-label"><strong>Select Department to Assign</strong></label>
        <select id="departmentSelect" class="form-select" required>
            <option value="">-- Choose a Department --</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
            @endforeach
        </select>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="button" class="btn btn-primary flex-grow-1" onclick="assignTaskToDepartment({{ $task->id }})">
            <i class="fe fe-check"></i> Assign
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fe fe-x"></i> Cancel
        </button>
    </div>
</div>

<script>
    function assignTaskToDepartment(taskId) {
        const departmentId = document.getElementById('departmentSelect').value;

        if (!departmentId) {
            alert('Please select a department');
            return;
        }

        fetch(`/surveillance/tasks/${taskId}/assign-to-department/${departmentId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('assignTaskModal'));
                modal.hide();

                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="fe fe-check-circle"></i> ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.insertBefore(alertDiv, document.body.firstChild);

                // Reload page after 2 seconds
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while assigning the task');
        });
    }
</script>
