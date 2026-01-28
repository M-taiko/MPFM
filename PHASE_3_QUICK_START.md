# Phase 3: Quick Start Guide

**Phase:** 3.1 Complete | 3.2-3.4 Pending
**Status:** ✅ Active Development

---

## 🚀 What's New in Phase 3.1

### MPFM Domain Models
```php
use App\Domains\MPFM\Models\MPFMTest;
use App\Domains\MPFM\Models\TestResult;
use App\Domains\MPFM\Models\TestTemplate;
use App\Domains\MPFM\Models\TestLog;
```

### Bulk Upload Models
```php
use App\Models\BulkUpload;
use App\Models\BulkUploadRow;
```

---

## 📝 Usage Examples

### Working with MPFM Tests
```php
// Create MPFM test
$test = MPFMTest::create([
    'well_test_id' => 1,
    'department_id' => 1, // MPFM
    'test_date' => now(),
    'test_duration_minutes' => 120,
]);

// Add test results
$test->testResults()->create([
    'parameter_name' => 'GOR',
    'value' => 1234.56,
    'unit' => 'scf/stb',
    'status' => 'recorded',
]);

// Get tests for department
$tests = MPFMTest::forDepartment(1)->recent()->get();

// Check if processing
if ($test->isProcessed()) {
    echo "Done processing";
}
```

### Test Templates
```php
// Get default template for department
$template = TestTemplate::forDepartment(1)->default()->first();

// Get required fields
$fields = $template->getRequiredFields();

// Create new version
$template->createNewVersion([
    'required_fields' => ['GOR', 'Temperature'],
    'validation_rules' => [...]
], 'Updated field list');

// Set as default
$template->setAsDefault();

// Duplicate template
$newTemplate = $template->duplicate('New Copy');
```

### Bulk Uploads
```php
// Start bulk upload
$upload = BulkUpload::create([
    'department_id' => 1,
    'uploaded_by_user_id' => auth()->id(),
    'file_name' => 'tests.csv',
    'file_type' => 'csv',
    'file_size_bytes' => 50000,
    'file_hash' => hash_file('sha256', $path),
]);

// Add rows
for ($i = 1; $i <= 100; $i++) {
    $upload->rows()->create([
        'row_number' => $i,
        'raw_data' => $data[$i],
        'status' => 'pending',
    ]);
}

// Process upload
$upload->markAsProcessing();
// ... process rows ...
$upload->markAsCompleted();

// Get success rate
$rate = $upload->getSuccessRate(); // e.g., 95.5
```

### Test Results
```php
// Get specific test result
$gor = $test->getResultByParameter('GOR');

// Get all results
$results = $test->getAllResults();

// Validate result
$result->validate();

// Approve result
$result->approve();

// Check if valid
if ($result->isValid()) {
    // Use in calculations
}
```

---

## 🗄️ Database Tables

### test_results
Stores individual test parameter results
```
├── id (PK)
├── mpfm_test_id (FK)
├── parameter_name
├── value (decimal)
├── unit
├── status (recorded|validated|approved)
└── timestamps
```

### test_templates
Pre-configured test templates
```
├── id (PK)
├── department_id (FK)
├── name
├── template_type
├── required_fields (JSON)
├── optional_fields (JSON)
├── validation_rules (JSON)
└── timestamps
```

### mpfm_test_logs
Bulk upload file tracking
```
├── id (PK)
├── mpfm_test_id (FK)
├── uploaded_by_user_id (FK)
├── file_name
├── file_hash (unique)
├── parsing_status
├── records_count
├── records_imported
└── timestamps
```

### bulk_uploads
Batch operation tracking
```
├── id (PK)
├── department_id (FK)
├── uploaded_by_user_id (FK)
├── file_name
├── file_type
├── status (pending|processing|completed|failed)
├── total_rows
├── successful_rows
├── failed_rows
└── timestamps
```

### bulk_upload_rows
Individual row processing
```
├── id (PK)
├── bulk_upload_id (FK)
├── row_number
├── raw_data (JSON)
├── status (pending|processed|successful|failed)
├── error_message
├── created_record_id
└── timestamps
```

---

## 🔍 Useful Queries

### Get recent tests
```php
$tests = MPFMTest::forDepartment(1)->recent()->limit(10)->get();
```

### Find processed tests
```php
$tests = MPFMTest::where('processing_status', 'completed')->get();
```

### Get active templates
```php
$templates = TestTemplate::forDepartment(1)->active()->get();
```

### Find failed uploads
```php
$uploads = BulkUpload::failed()->orderByDesc('created_at')->get();
```

### Get upload statistics
```php
$upload = BulkUpload::find(1);
$successRate = $upload->getSuccessRate();
$failedRows = $upload->rows()->failed()->count();
```

---

## ⚙️ Configuration

### MPFMTest Processing
```php
if ($test->processing_status === 'pending') {
    $test->markAsProcessing();

    try {
        // Process test results
        $test->markAsCompleted();
    } catch (Exception $e) {
        $test->markAsFailed(['error' => $e->getMessage()]);
    }
}
```

### Template Management
```php
// Create template for department
$template = TestTemplate::create([
    'department_id' => 1,
    'name' => 'Standard MPFM Test',
    'template_type' => 'mpfm_test',
    'required_fields' => ['GOR', 'Temperature', 'Pressure'],
    'validation_rules' => [
        'GOR' => 'required|numeric|min:0',
        'Temperature' => 'required|numeric|min:0|max:500',
    ],
]);

$template->setAsDefault();
```

---

## 🧪 Testing

### Unit Test Example
```php
public function test_can_create_mpfm_test()
{
    $test = MPFMTest::factory()->create();
    $this->assertNotNull($test->id);
}

public function test_can_add_test_results()
{
    $test = MPFMTest::factory()->create();
    $test->testResults()->create([
        'parameter_name' => 'GOR',
        'value' => 1234.56,
    ]);

    $this->assertTrue($test->hasResults());
}
```

---

## 📚 Documentation Files

- `PHASE_3_IMPLEMENTATION_PLAN.md` - Full implementation details
- `PHASE_3_STATUS.md` - Current status and progress
- `PHASE_3_QUICK_START.md` - This file

---

## 🔗 Key Classes

| Class | Location | Purpose |
|-------|----------|---------|
| MPFMTest | `App\Domains\MPFM\Models` | Core MPFM test model |
| TestResult | `App\Domains\MPFM\Models` | Test parameter results |
| TestTemplate | `App\Domains\MPFM\Models` | Test templates |
| TestLog | `App\Domains\MPFM\Models` | Upload tracking |
| BulkUpload | `App\Models` | Batch operations |
| BulkUploadRow | `App\Models` | Individual rows |

---

## 📝 Next Steps

1. ✅ Phase 3.1: MPFM Domain Consolidation (COMPLETE)
2. ⏳ Phase 3.2: Department Workflow Configuration
3. ⏳ Phase 3.3: Analytics & Reporting
4. ⏳ Phase 3.4: Enhanced Features

---

## 🎯 Upcoming Features

- Department-specific workflows
- Custom state machines
- SLA configuration per state
- Department test types
- Analytics dashboards
- Performance reports
- Notification system
- Custom field support

---

**Phase 3.1 Status:** ✅ Complete
**Total Migrations:** 56/56 ✅
**Ready for:** Phase 3.2 Implementation
