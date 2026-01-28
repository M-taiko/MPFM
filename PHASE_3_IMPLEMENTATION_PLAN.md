# Phase 3: MPFM Domain Consolidation & Expansion

**Date:** January 28, 2026
**Status:** Planning & Implementation
**Objective:** Consolidate MPFM operations into unified domain structure with multi-department support

---

## 🎯 Phase 3 Overview

Consolidate the MPFM-specific domain into a generalized multi-department framework while maintaining backward compatibility and expanding capabilities for 7 departments (MPFM, Wire Line, Production, Drilling, Completion, Workover, Maintenance).

---

## Phase 3 Goals

### 1. Domain Consolidation
- Migrate MPFM operations to domain-driven structure
- Support multi-department MPFM operations
- Maintain backward compatibility with existing MPFM tests
- Implement shared test data models

### 2. Multi-Department Expansion
- Configure workflow settings per department
- Department-specific test types and procedures
- Department-level SLA rules
- Custom field support per department

### 3. Enhanced Analytics
- Department performance dashboards
- Cross-department comparison reports
- Predictive analytics for task completion
- Resource utilization metrics

### 4. Operational Features
- Bulk test uploads per department
- Automated report generation
- Task template system
- Department-specific notifications

---

## Implementation Phases

### Phase 3.1: MPFM Domain Consolidation (Days 1-2)

#### Database Migrations
```php
// 2026_01_30_000000_create_mpfm_domain_structure.php
- Migrate MPFMTest to App\Domains\MPFM\Models\MPFMTest
- Create test_results table for normalized test data
- Add workflow_config to mpfm_tests table
- Create mpfm_test_logs table for bulk uploads

// 2026_01_30_000001_add_department_to_mpfm_tests.php
- Add department_id to mpfm_tests
- Add is_active flag
- Create composite indexes

// 2026_01_30_000002_create_test_templates.php
- Create test_templates table
- Template fields configuration
- Department-specific templates

// 2026_01_30_000003_create_test_logs_storage.php
- Create mpfm_test_logs_files table
- Log file metadata
- Upload history tracking
```

#### Models
```php
App\Domains\MPFM\Models\
├── MPFMTest.php
├── TestResult.php
├── TestTemplate.php
├── TestLog.php
└── TestField.php
```

#### Services
```php
App\Domains\MPFM\Services\
├── MPFMTestService.php
├── TestValidationService.php
├── TestCalculationService.php
├── BulkUploadService.php
├── TestReportService.php
└── TestAnalyticsService.php
```

---

### Phase 3.2: Department Workflow Configuration (Days 2-3)

#### Database Migrations
```php
// 2026_01_31_000000_create_department_workflows.php
Schema::create('department_workflows', function (Blueprint $table) {
    $table->id();
    $table->foreignId('department_id')->constrained()->cascadeOnDelete();
    $table->json('states'); // Custom states per department
    $table->json('transitions'); // Custom transitions
    $table->json('sla_rules'); // SLA config per state
    $table->json('notifications'); // Notification settings
    $table->json('custom_fields'); // Custom fields
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->index('department_id');
});

// 2026_01_31_000001_create_department_test_types.php
Schema::create('department_test_types', function (Blueprint $table) {
    $table->id();
    $table->foreignId('department_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->text('description');
    $table->json('required_fields');
    $table->json('validation_rules');
    $table->integer('estimated_duration_minutes');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->unique(['department_id', 'name']);
});

// 2026_01_31_000002_create_department_sla_rules.php
Schema::create('department_sla_rules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('department_id')->constrained()->cascadeOnDelete();
    $table->string('state');
    $table->integer('warning_minutes');
    $table->integer('critical_minutes');
    $table->integer('escalation_level');
    $table->timestamps();
    $table->unique(['department_id', 'state']);
});
```

#### Models
```php
App\Domains\MPFM\Models\
├── DepartmentWorkflow.php
├── DepartmentTestType.php
└── DepartmentSLARule.php
```

#### Services
```php
App\Services\
├── DepartmentWorkflowService.php
├── WorkflowConfigurationService.php
└── DepartmentSLAConfigService.php
```

---

### Phase 3.3: Analytics & Reporting (Days 3-4)

#### Database Migrations
```php
// 2026_02_01_000000_create_analytics_tables.php
Schema::create('department_metrics', function (Blueprint $table) {
    $table->id();
    $table->foreignId('department_id')->constrained()->cascadeOnDelete();
    $table->date('metric_date');
    $table->integer('total_tasks');
    $table->integer('completed_tasks');
    $table->integer('overdue_tasks');
    $table->decimal('completion_rate', 5, 2);
    $table->decimal('average_completion_time', 8, 2);
    $table->timestamps();
    $table->unique(['department_id', 'metric_date']);
    $table->index(['department_id', 'metric_date']);
});

Schema::create('test_statistics', function (Blueprint $table) {
    $table->id();
    $table->foreignId('mpfm_test_id')->nullable()->constrained('mpfm_tests')->nullOnDelete();
    $table->foreignId('department_id')->constrained()->cascadeOnDelete();
    $table->decimal('average_value', 10, 2);
    $table->decimal('min_value', 10, 2);
    $table->decimal('max_value', 10, 2);
    $table->decimal('std_deviation', 10, 4);
    $table->integer('sample_count');
    $table->timestamps();
});
```

#### Services
```php
App\Services\
├── DepartmentAnalyticsService.php
├── PerformanceMetricsService.php
├── PredictiveAnalyticsService.php
└── ReportGenerationService.php
```

#### Views
```
resources/views/
├── analytics/
│   ├── dashboard.blade.php
│   ├── department-comparison.blade.php
│   ├── performance-metrics.blade.php
│   └── predictive-analysis.blade.php
├── reports/
│   ├── department-report.blade.php
│   ├── test-analysis.blade.php
│   └── sla-compliance.blade.php
└── dashboards/
    ├── mpfm-operations.blade.php
    └── enterprise-overview.blade.php
```

---

### Phase 3.4: Enhanced Features (Days 4-5)

#### 1. Bulk Upload System
```php
// Features:
- CSV/Excel upload support
- Batch processing with queues
- Validation before processing
- Error reporting and retry
- Upload history and logging
```

#### 2. Template System
```php
// Features:
- Pre-configured test templates
- Department-specific templates
- Template versioning
- Template marketplace
```

#### 3. Notification System
```php
// Features:
- Department-specific notification rules
- Multi-channel notifications (Email, SMS, In-app)
- Notification preferences per user/role
- Notification history and analytics
```

#### 4. Custom Fields
```php
// Features:
- Dynamic custom fields per department
- Field validation rules
- Field dependencies
- Custom field versioning
```

---

## Critical Database Tables Summary

### New Tables (Phase 3)
1. **department_workflows** - Custom workflow configurations
2. **department_test_types** - Department-specific test types
3. **department_sla_rules** - SLA configuration per department
4. **department_metrics** - Daily department metrics
5. **test_statistics** - Statistical analysis of tests
6. **test_templates** - Pre-configured test templates
7. **test_logs_files** - Bulk upload tracking
8. **bulk_uploads** - Bulk operation tracking

---

## API Endpoints (Phase 3)

### Department Configuration
```
GET    /api/departments/{id}/workflow          - Get workflow config
PUT    /api/departments/{id}/workflow          - Update workflow
GET    /api/departments/{id}/test-types        - List test types
POST   /api/departments/{id}/test-types        - Create test type
PUT    /api/departments/{id}/sla-rules         - Update SLA rules
```

### Analytics
```
GET    /api/analytics/department/{id}          - Department analytics
GET    /api/analytics/comparison               - Cross-department comparison
GET    /api/analytics/performance              - Performance metrics
GET    /api/analytics/predictions              - Predictive analytics
```

### Bulk Operations
```
POST   /api/bulk-upload                        - Upload bulk data
GET    /api/bulk-uploads/{id}                  - Get upload status
GET    /api/bulk-uploads/{id}/results          - Get upload results
```

### Reports
```
GET    /api/reports/department/{id}            - Department report
GET    /api/reports/test-analysis              - Test analysis
GET    /api/reports/sla-compliance             - SLA compliance report
```

---

## Testing Strategy

### Unit Tests
- Department workflow logic
- Calculation services
- Validation rules
- Analytics computations

### Integration Tests
- Multi-department workflows
- Bulk upload processing
- Report generation
- Analytics data pipeline

### Feature Tests
- End-to-end workflows
- API endpoints
- User permissions
- Department isolation

---

## Rollout Plan

### Week 1 (Phase 3.1 & 3.2)
- Days 1-2: Domain consolidation
- Days 2-3: Workflow configuration
- Testing and verification

### Week 2 (Phase 3.3 & 3.4)
- Days 1-2: Analytics implementation
- Days 2-3: Enhanced features
- Days 4-5: Testing and documentation

### Week 3: Stabilization
- UAT (User Acceptance Testing)
- Bug fixes and optimizations
- Performance tuning
- Documentation updates

---

## Success Criteria

✅ All Phase 3 migrations pass
✅ Multi-department workflows functional
✅ Analytics dashboards display correctly
✅ Bulk upload system operational
✅ Report generation working
✅ All tests passing (>85% coverage)
✅ Performance metrics acceptable (<2s page load)
✅ Documentation complete

---

## Next Actions

1. ✅ Review Phase 3 plan
2. ⏳ Get stakeholder approval
3. ⏳ Create detailed sprint plan
4. ⏳ Begin Phase 3.1 implementation

---

**Status:** Ready for approval and implementation
**Estimated Timeline:** 3 weeks
**Team Size:** 2-3 developers
**Risk Level:** Medium (new domain structure, but Phase 2 foundation solid)
