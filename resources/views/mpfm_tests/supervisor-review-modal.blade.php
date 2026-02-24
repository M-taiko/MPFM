<!-- Supervisor Review Decision Modal -->
<div class="modal fade" id="supervisorReviewModal" tabindex="-1" role="dialog" aria-labelledby="supervisorReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="supervisorReviewModalLabel">
                    <i class="fe fe-check-square mr-2"></i>Supervisor Review Decision
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Test Summary Section -->
                <div class="alert alert-info" role="alert">
                    <h6 class="alert-heading"><i class="fe fe-info mr-2"></i>Test Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Test Date:</strong> {{ $mpfmTest->test_date->format('Y-m-d') }}</p>
                            <p class="mb-2"><strong>Operator:</strong> {{ $mpfmTest->user->name ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>Oil Field:</strong> {{ $mpfmTest->oilField->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Tower:</strong> {{ $mpfmTest->tower->name ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>Oil String:</strong> {{ $mpfmTest->oilString->name ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>Status:</strong>
                                <span class="badge badge-info">{{ ucfirst($mpfmTest->status) }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fe fe-bar-chart-2 mr-2"></i>Key Metrics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <p class="text-muted mb-1">Oil Rate</p>
                                <p class="h5 text-primary">{{ number_format($kpis['oil_rate'] ?? 0, 2) }} <small>STB/d</small></p>
                            </div>
                            <div class="col-md-4 text-center">
                                <p class="text-muted mb-1">Gas Rate</p>
                                <p class="h5 text-primary">{{ number_format($kpis['gas_rate'] ?? 0, 2) }} <small>Mscf/d</small></p>
                            </div>
                            <div class="col-md-4 text-center">
                                <p class="text-muted mb-1">Water Cut</p>
                                <p class="h5 text-primary">{{ number_format($mpfmTest->water_cut ?? 0, 2) }} <small>%</small></p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4 text-center">
                                <p class="text-muted mb-1">GOR</p>
                                <p class="h5 text-info">{{ number_format($mpfmTest->calculated_kpis['gor'] ?? 0, 2) }} <small>Scf/STB</small></p>
                            </div>
                            <div class="col-md-4 text-center">
                                <p class="text-muted mb-1">Duration</p>
                                <p class="h5 text-info">{{ number_format($mpfmTest->test_duration_hours ?? 0, 2) }} <small>hrs</small></p>
                            </div>
                            <div class="col-md-4 text-center">
                                <p class="text-muted mb-1">Pressure</p>
                                <p class="h5 text-info">{{ number_format($kpis['avg_pressure'] ?? $kpis['pressure'] ?? 0, 2) }} <small>psi</small></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review Notes -->
                <div class="form-group">
                    <label for="supervisorRemarks"><i class="fe fe-edit mr-2"></i>Review Remarks (Optional)</label>
                    <textarea class="form-control" id="supervisorRemarks" name="supervisor_remarks" rows="3" placeholder="Enter your remarks about this test..."></textarea>
                    <small class="form-text text-muted">Provide any additional feedback or observations about the test data.</small>
                </div>

                <!-- Decision Options -->
                <div class="alert alert-warning" role="alert">
                    <h6 class="alert-heading"><i class="fe fe-alert-circle mr-2"></i>Make a Decision</h6>
                    <p class="mb-0">Select one of the following actions:</p>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <!-- Reject Button -->
                <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#rejectReasonCollapse">
                    <i class="fe fe-x-circle mr-2"></i>Reject Test
                </button>

                <!-- Re-test Button -->
                <button type="button" class="btn btn-warning" data-toggle="collapse" data-target="#retestReasonCollapse">
                    <i class="fe fe-repeat-2 mr-2"></i>Request Re-test
                </button>

                <!-- Approve Button -->
                <button type="button" class="btn btn-success" id="approveTestBtn">
                    <i class="fe fe-check-circle mr-2"></i>Approve Test
                </button>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fe fe-x mr-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Reason Modal -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1" role="dialog" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectReasonModalLabel">
                    <i class="fe fe-alert-circle mr-2"></i>Reject Test
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="rejectTestForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejectionReason"><strong>Rejection Reason *</strong></label>
                        <select class="form-control" id="rejectionReason" name="rejection_reason" required>
                            <option value="">-- Select Reason --</option>
                            <option value="invalid_data">Invalid or Inconsistent Data</option>
                            <option value="equipment_failure">Equipment Failure</option>
                            <option value="test_interrupted">Test Interrupted/Not Completed</option>
                            <option value="calibration_issue">Calibration Issue</option>
                            <option value="environmental_conditions">Adverse Environmental Conditions</option>
                            <option value="data_quality">Poor Data Quality</option>
                            <option value="other">Other (Please Specify)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="rejectionDetails"><strong>Detailed Reason</strong></label>
                        <textarea class="form-control" id="rejectionDetails" name="rejection_details" rows="4" placeholder="Provide detailed explanation for rejection..."></textarea>
                        <small class="form-text text-muted">This will be sent to the operator so they understand what to fix.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fe fe-send mr-2"></i>Reject & Notify Operator
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Re-test Request Modal -->
<div class="modal fade" id="retestRequestModal" tabindex="-1" role="dialog" aria-labelledby="retestRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="retestRequestModalLabel">
                    <i class="fe fe-repeat-2 mr-2"></i>Request Re-test
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="retestRequestForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info" role="alert">
                        <i class="fe fe-info mr-2"></i>
                        <strong>This action will create a new task for the operator to perform a new test.</strong>
                        <br>The test data will be retained for reference, but a fresh test will be required.
                    </div>

                    <div class="form-group">
                        <label for="retestReason"><strong>Reason for Re-test Request *</strong></label>
                        <select class="form-control" id="retestReason" name="retest_reason" required>
                            <option value="">-- Select Reason --</option>
                            <option value="data_inconsistency">Data Inconsistency Detected</option>
                            <option value="incomplete_test">Test Appeared Incomplete</option>
                            <option value="equipment_suspect">Equipment Performance Suspect</option>
                            <option value="validation_failed">Failed Validation Checks</option>
                            <option value="trend_unusual">Unusual Production Trends</option>
                            <option value="anomaly_detected">Anomaly Detected in Data</option>
                            <option value="other">Other (Please Specify)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="retestDetails"><strong>Detailed Instructions</strong></label>
                        <textarea class="form-control" id="retestDetails" name="retest_details" rows="4" placeholder="Provide detailed instructions for the re-test..."></textarea>
                        <small class="form-text text-muted">Explain what should be done differently in the re-test and any specific requirements.</small>
                    </div>

                    <div class="form-group">
                        <label for="expectedRetestDate"><strong>Expected Re-test Date *</strong></label>
                        <input type="date" class="form-control" id="expectedRetestDate" name="expected_retest_date" required>
                    </div>

                    <div class="form-group">
                        <label for="retestPriority"><strong>Priority Level *</strong></label>
                        <select class="form-control" id="retestPriority" name="retest_priority" required>
                            <option value="">-- Select Priority --</option>
                            <option value="high">High Priority</option>
                            <option value="normal">Normal Priority</option>
                            <option value="low">Low Priority</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fe fe-send mr-2"></i>Create Re-test Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve Confirmation Modal -->
<div class="modal fade" id="approveConfirmModal" tabindex="-1" role="dialog" aria-labelledby="approveConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveConfirmModalLabel">
                    <i class="fe fe-check-circle mr-2"></i>Confirm Approval
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="approveTestForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success" role="alert">
                        <i class="fe fe-check-square mr-2"></i>
                        <strong>You are about to approve this MPFM test.</strong>
                    </div>

                    <p><strong>The following will happen:</strong></p>
                    <ul class="list-unstyled">
                        <li id="approvalStepDept" style="display: none;">
                            <i class="fe fe-arrow-right mr-2 text-info"></i>
                            <span class="text-info">Test status will be updated to "Approved by Department"</span>
                        </li>
                        <li id="approvalStepSurv" style="display: none;">
                            <i class="fe fe-arrow-right mr-2 text-success"></i>
                            <span class="text-success">Test will be forwarded to Surveillance Supervisor</span>
                        </li>
                        <li id="approvalStepJob" style="display: none;">
                            <i class="fe fe-arrow-right mr-2 text-success"></i>
                            <span class="text-success">Test will be automatically forwarded to Job Officer</span>
                        </li>
                        <li>
                            <i class="fe fe-mail mr-2 text-primary"></i>
                            <span class="text-primary">Relevant users will be notified via email</span>
                        </li>
                    </ul>

                    <div class="form-group mt-3">
                        <label for="approvalNotes"><strong>Approval Comments (Optional)</strong></label>
                        <textarea class="form-control" id="approvalNotes" name="approval_notes" rows="3" placeholder="Add any comments about this approval..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fe fe-check mr-2"></i>Confirm Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal-header {
        border-bottom: 2px solid rgba(0,0,0,0.1);
    }

    .modal-body .card {
        border: 1px solid #e3e6f0;
    }

    .modal-body .card-header {
        border-bottom: 1px solid #e3e6f0;
    }

    .modal-footer {
        border-top: 1px solid #e3e6f0;
    }

    .alert {
        border-radius: 6px;
    }

    .form-group label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn {
        border-radius: 4px;
        font-weight: 500;
    }

    .modal-body .row {
        margin-bottom: 1rem;
    }

    .modal-body .col-md-4 {
        padding: 1rem;
        border-right: 1px solid #e3e6f0;
    }

    .modal-body .col-md-4:last-child {
        border-right: none;
    }

    .text-muted {
        color: #6c757d;
    }

    .h5 {
        font-weight: 600;
        margin: 0.5rem 0;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get user role from data attribute
    const userRole = document.querySelector('[data-user-role]')?.getAttribute('data-user-role') || 'department_supervisor';

    // Handle Approve button click
    document.getElementById('approveTestBtn')?.addEventListener('click', function() {
        // Set appropriate form action based on user role
        const testId = {{ $mpfmTest->id }};
        let actionUrl = '';

        if (userRole === 'department_supervisor') {
            actionUrl = `/mpfm-tests/${testId}/approve-by-department`;
            document.getElementById('approvalStepDept').style.display = 'flex';
            document.getElementById('approvalStepSurv').style.display = 'flex';
        } else if (userRole === 'surveillance_supervisor') {
            actionUrl = `/mpfm-tests/${testId}/approve-by-surveillance`;
            document.getElementById('approvalStepJob').style.display = 'flex';
        }

        document.getElementById('approveTestForm').action = actionUrl;
        const approveModal = new bootstrap.Modal(document.getElementById('approveConfirmModal'));
        approveModal.show();
    });

    // Handle Reject button click
    document.querySelector('[data-toggle="collapse"][data-target="#rejectReasonCollapse"]')?.addEventListener('click', function() {
        const testId = {{ $mpfmTest->id }};
        document.getElementById('rejectTestForm').action = `/mpfm-tests/${testId}/reject`;
        const rejectModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
        rejectModal.show();
    });

    // Handle Re-test button click
    document.querySelector('[data-toggle="collapse"][data-target="#retestReasonCollapse"]')?.addEventListener('click', function() {
        const testId = {{ $mpfmTest->id }};
        document.getElementById('retestRequestForm').action = `/mpfm-tests/${testId}/request-retest`;
        const retestModal = new bootstrap.Modal(document.getElementById('retestRequestModal'));
        retestModal.show();
    });
});
</script>
