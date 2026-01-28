document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".btn-assign").forEach(button => {
        button.addEventListener("click", function() {
            const taskId = this.getAttribute("data-task-id");
            const taskTitle = this.getAttribute("data-task-title");
            loadAssignmentModal(taskId, taskTitle);
        });
    });
});

function loadAssignmentModal(taskId, taskTitle) {
    const modalContent = document.getElementById("modalContent");
    const assignmentModalLabel = document.getElementById("assignmentModalLabel");

    assignmentModalLabel.textContent = "Assign Operators to: " + taskTitle;
    modalContent.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>';

    fetch("/department-tasks/api/get-assignment-modal", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content"),
            "Accept": "application/json"
        },
        body: JSON.stringify({ task_id: taskId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderAssignmentModal(data, taskId);
        } else {
            modalContent.innerHTML = "<div class=\"alert alert-danger\">Error loading modal</div>";
        }
    })
    .catch(error => {
        console.error("Error:", error);
        modalContent.innerHTML = "<div class=\"alert alert-danger\">An error occurred</div>";
    });
}

function renderAssignmentModal(data, taskId) {
    const modalContent = document.getElementById("modalContent");
    const task = data.task;
    const operators = data.operators;
    const assignedOperators = data.assignedOperators;

    let html = "<div class=\"mb-3\"><label class=\"form-label\"><strong>Task:</strong></label>";
    html += "<p class=\"form-control-plaintext\">" + task.title + "</p></div>";
    html += "<div class=\"mb-3\"><label for=\"operatorSelect\" class=\"form-label\"><strong>Select Operator</strong></label>";
    html += "<select class=\"form-select\" id=\"operatorSelect\"><option value=\"\">-- Choose an operator --</option>";

    operators.forEach(op => {
        const isAssigned = assignedOperators.includes(op.id);
        const disabled = isAssigned ? " disabled" : "";
        const selected = isAssigned ? " (Already Assigned)" : "";
        html += "<option value=\"" + op.id + "\"" + disabled + ">" + op.name + selected + "</option>";
    });

    html += "</select></div>";

    if (assignedOperators.length > 0) {
        html += "<div class=\"mb-3\"><label class=\"form-label\"><strong>Assigned Operators</strong></label>";
        html += "<div class=\"list-group\">";

        assignedOperators.forEach(opId => {
            const operator = operators.find(o => o.id === opId);
            if (operator) {
                html += "<div class=\"list-group-item d-flex justify-content-between align-items-center\">";
                html += "<span>" + operator.name + "</span>";
                html += "<button type=\"button\" class=\"btn btn-sm btn-danger btn-remove-operator\" data-task-id=\"" + task.id + "\" data-operator-id=\"" + operator.id + "\">";
                html += "<i class=\"fe fe-trash\"></i> Remove</button></div>";
            }
        });

        html += "</div></div>";
    }

    html += "<button type=\"button\" class=\"btn btn-primary w-100 mt-3\" id=\"btnAssignOperator\">";
    html += "<i class=\"fe fe-plus\"></i> Assign Selected Operator</button>";

    modalContent.innerHTML = html;

    document.getElementById("btnAssignOperator").addEventListener("click", function() {
        assignOperator(taskId);
    });

    document.querySelectorAll(".btn-remove-operator").forEach(btn => {
        btn.addEventListener("click", function() {
            const tid = this.getAttribute("data-task-id");
            const oid = this.getAttribute("data-operator-id");
            removeOperator(tid, oid);
        });
    });
}

function assignOperator(taskId) {
    const operatorSelect = document.getElementById("operatorSelect");
    const operatorId = operatorSelect.value;

    if (!operatorId) {
        alert("Please select an operator");
        return;
    }

    const btn = document.getElementById("btnAssignOperator");
    btn.disabled = true;
    btn.innerHTML = "<i class=\"fe fe-loader\"></i> Assigning...";

    fetch("/department-tasks/api/assign-operator", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content"),
            "Accept": "application/json"
        },
        body: JSON.stringify({
            task_id: taskId,
            operator_id: operatorId,
            is_primary: false
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const taskTitle = document.querySelector("[data-task-id=\"" + taskId + "\"]").getAttribute("data-task-title");
            loadAssignmentModal(taskId, taskTitle);
            showAlert("success", "Operator assigned successfully!");
        } else {
            showAlert("danger", "Failed to assign operator");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        showAlert("danger", "An error occurred");
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = "<i class=\"fe fe-plus\"></i> Assign Selected Operator";
    });
}

function removeOperator(taskId, operatorId) {
    if (!confirm("Are you sure you want to remove this operator?")) {
        return;
    }

    fetch("/department-tasks/api/remove-operator", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content"),
            "Accept": "application/json"
        },
        body: JSON.stringify({
            task_id: taskId,
            operator_id: operatorId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const taskTitle = document.querySelector("[data-task-id=\"" + taskId + "\"]").getAttribute("data-task-title");
            loadAssignmentModal(taskId, taskTitle);
            showAlert("success", "Operator removed successfully!");
        } else {
            showAlert("danger", "Failed to remove operator");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        showAlert("danger", "An error occurred");
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement("div");
    alertDiv.className = "alert alert-" + type + " alert-dismissible fade show";
    alertDiv.setAttribute("role", "alert");
    alertDiv.innerHTML = message + "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>";

    const container = document.querySelector(".container-fluid");
    if (container) {
        container.insertBefore(alertDiv, container.firstChild.nextSibling);
    } else {
        document.body.insertBefore(alertDiv, document.body.firstChild);
    }

    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
