# Upload Blade File Structure

**File:** `resources/views/logs/upload.blade.php`

---

## 📋 Complete Blade File Structure

```
┌─────────────────────────────────────────────────────┐
│ UPLOAD FORM BLADE (upload.blade.php)                │
└─────────────────────────────────────────────────────┘

1. HEADER SECTION
   ├─ Breadcrumb Navigation
   │  ├─ Home
   │  ├─ My Tasks
   │  └─ Upload Log for {Task ID}
   │
   ├─ Page Title
   │  ├─ Icon: Upload Cloud
   │  ├─ Title: "Upload Test Log" (if task)
   │  └─ Subtitle: "Upload and analyze VX log files"
   │
   └─ Back Button
      └─ "Back to Task" link

2. TASK CONTEXT CARD (Only if $task is set)
   ├─ Task Identifier
   │  └─ Display: Task: TASK-001
   │
   ├─ Field Information
   │  ├─ Field: (from tower)
   │  ├─ Tower: (from tower model)
   │  ├─ String: (from oil string)
   │  └─ MPFM Device: (from task)
   │
   ├─ Task Instructions (if available)
   │  └─ Display: operator task_instructions
   │
   └─ Expected Values (if available)
      ├─ Oil Rate
      ├─ Gas Rate
      └─ Water Cut

3. ALERT MESSAGES
   ├─ Success Alert
   ├─ Error Alert
   └─ Form Validation Errors

4. UPLOAD FORM
   │
   ├─ FORM START
   │  ├─ Method: POST
   │  ├─ Action: /process-log
   │  ├─ Enctype: multipart/form-data
   │  └─ CSRF: @csrf
   │
   ├─ SECTION 1: Well Information
   │  ├─ Label: "✓ Auto-filled from task" (if $task)
   │  │
   │  └─ Fields:
   │     ├─ Client (readonly if task)
   │     │  └─ Value: {{ $autoFillData['client'] }}
   │     │
   │     ├─ Field (readonly if task)
   │     │  └─ Value: {{ $autoFillData['field'] }}
   │     │
   │     └─ Well Name (readonly if task)
   │        └─ Value: {{ $autoFillData['well'] }}
   │
   ├─ DIVIDER
   │
   ├─ SECTION 2: Test Configuration
   │  ├─ Label: "✓ Auto-filled from task" (if $task)
   │  │
   │  └─ Fields (4 columns):
   │     ├─ Wellhead Tower (readonly if task)
   │     │  └─ Value: {{ $autoFillData['wellhead_tower'] }}
   │     │
   │     ├─ Wellhead String (readonly if task)
   │     │  └─ Value: {{ $autoFillData['wellhead_string'] }}
   │     │
   │     ├─ Choke Size (1/64")
   │     │  └─ Type: number (editable)
   │     │
   │     └─ Operator Name (readonly if task)
   │        └─ Value: {{ $autoFillData['operator'] }}
   │
   ├─ MORE FIELDS (2 columns):
   │  ├─ Test Date (readonly if task)
   │  │  └─ Type: date
   │  │  └─ Value: {{ $autoFillData['test_date'] }}
   │  │
   │  └─ Test Type (readonly if task)
   │     └─ Dropdown: flow, cleanup, build-up
   │     └─ Value: {{ $autoFillData['test_type'] }}
   │
   ├─ DIVIDER
   │
   ├─ SECTION 3: VX Log File
   │  │
   │  ├─ File Upload Area (Drag & Drop)
   │  │  ├─ Icon: Cloud Upload
   │  │  ├─ Text: "Click to browse or drag & drop"
   │  │  ├─ Hint: "Supported formats: .vxlog, .txt"
   │  │  └─ Hidden Input: file upload
   │  │
   │  └─ File Selected Display
   │     ├─ File Icon
   │     ├─ File Name
   │     ├─ File Size
   │     └─ Check Mark
   │
   ├─ DIVIDER
   │
   └─ SUBMIT BUTTON
      └─ "Upload & Analyze Log File" (Primary)

5. FORM HIDDEN FIELDS
   └─ task_id (if $task is set)
      └─ Value: {{ $task->id }}

6. JAVASCRIPT
   ├─ File upload handling
   ├─ Drag & drop support
   ├─ File size calculation
   └─ UI updates on file select
```

---

## 🎨 Key Features of Blade File

### Auto-Fill Indicators
```blade
@if(isset($task))
<span style="...">
    <i class="fe fe-check-circle"></i> Auto-filled from task
</span>
@endif
```

### Read-Only Fields (when task provided)
```blade
<input type="text"
       value="{{ old('client', $autoFillData['client'] ?? '') }}"
       {{ isset($task) ? 'readonly' : '' }}
       required>
```

### Form Data Source
```blade
{{ old('fieldname', $autoFillData['fieldname'] ?? '') }}
```
- Uses old() for validation errors
- Falls back to $autoFillData from controller
- Falls back to empty string if not set

### Task Context Display
```blade
@if(isset($task))
    <!-- Show task information -->
    Task: {{ $task->task_identifier }}
    Field: {{ $task->oilString->tower->oilField->name }}
    Tower: {{ $task->oilString->tower->name }}
    String: {{ $task->oilString->name }}
@endif
```

---

## 📊 Data Flow in Blade

```
Controller sends:
├─ $task (PlanTask object)
└─ $autoFillData (array)

Blade receives:
├─ @if(isset($task)) ✓ Task provided
│  ├─ Display task context card
│  ├─ Show "Auto-filled from task" labels
│  ├─ Make fields readonly
│  └─ Include hidden task_id field
│
└─ @if(empty($task)) ✓ No task (generic upload)
   ├─ Show generic form
   ├─ Make fields editable
   └─ No task context displayed

Form values filled from:
$autoFillData = [
    'client' => 'ADNOC',
    'field' => $task->towerModel?->oilField?->name,
    'well' => $task->towerModel?->well_name,
    'wellhead_tower' => $task->towerModel?->name,
    'wellhead_string' => $task->oilString?->string_identifier,
    'operator' => auth()->user()->name,
    'test_date' => $task->scheduled_date?->format('Y-m-d'),
    'mpfm_device' => $task->category,
    'test_type' => $task->category,
]
```

---

## 🎯 Form Submission Flow

```
User Click: "Upload & Analyze Log File"
           ↓
Form Submit: POST /process-log
           ↓
Validation:
├─ logfile: required, file, mimes:txt,vxlog,log,csv, max:10240
├─ client: required, string
├─ field: required, string
├─ well: required, string
├─ operator: required, string
├─ test_date: required, date
└─ task_id: nullable, exists in plan_tasks
           ↓
Processing:
├─ Store file temporarily
├─ Parse/analyze file
├─ Prepare display data
├─ IF task_id provided:
│  └─ Mark task as COMPLETED ✅
└─ Show results
           ↓
Response:
└─ Redirect to logs.results view
   ├─ Show analysis
   ├─ Show charts
   └─ Show data tables
```

---

## 🔧 Key Form Sections with Code

### Section 1: Well Information (Auto-filled)
```blade
<div class="form-section-title">
    <i class="fe fe-info mr-2"></i>
    Well Information
    @if(isset($task))
    <span>✓ Auto-filled from task</span>
    @endif
</div>

<!-- Client Input -->
<input type="text" name="client"
       value="{{ old('client', $autoFillData['client'] ?? '') }}"
       {{ isset($task) ? 'readonly' : '' }}
       required>

<!-- Field Input -->
<input type="text" name="field"
       value="{{ old('field', $autoFillData['field'] ?? '') }}"
       {{ isset($task) ? 'readonly' : '' }}
       required>

<!-- Well Input -->
<input type="text" name="well"
       value="{{ old('well', $autoFillData['well'] ?? '') }}"
       {{ isset($task) ? 'readonly' : '' }}
       required>
```

### Section 2: Test Configuration (Auto-filled)
```blade
<div class="form-section-title">
    <i class="fe fe-settings mr-2"></i>
    Test Configuration
    @if(isset($task))
    <span>✓ Auto-filled from task</span>
    @endif
</div>

<!-- Wellhead Tower -->
<input type="text" name="wellhead_tower"
       value="{{ old('wellhead_tower', $autoFillData['wellhead_tower'] ?? '') }}"
       {{ isset($task) ? 'readonly' : '' }}>

<!-- Operator Name -->
<input type="text" name="operator"
       value="{{ old('operator', $autoFillData['operator'] ?? '') }}"
       {{ isset($task) ? 'readonly' : '' }}
       required>

<!-- Test Date -->
<input type="date" name="test_date"
       value="{{ old('test_date', $autoFillData['test_date'] ?? '') }}"
       {{ isset($task) ? 'readonly' : '' }}
       required>

<!-- Test Type -->
<select name="test_type" {{ isset($task) ? 'disabled' : '' }}>
    <option value="flow" {{ old('test_type', $autoFillData['test_type'] ?? '') == 'flow' ? 'selected' : '' }}>Flow Test</option>
    <option value="cleanup" {{ old('test_type', $autoFillData['test_type'] ?? '') == 'cleanup' ? 'selected' : '' }}>Clean Up</option>
    <option value="build-up" {{ old('test_type', $autoFillData['test_type'] ?? '') == 'build-up' ? 'selected' : '' }}>Build Up</option>
</select>
```

### Section 3: File Upload
```blade
<div class="form-section-title">
    <i class="fe fe-file-text mr-2"></i>
    VX Log File
</div>

<div class="file-upload-area">
    <input type="file" name="logfile"
           id="logfile"
           accept=".vxlog,.txt"
           required>

    <div class="file-upload-icon">
        <i class="fe fe-upload-cloud"></i>
    </div>
    <div class="file-upload-text">
        Click to browse or drag & drop your VX log file
    </div>
    <div class="file-upload-hint">
        Supported formats: .vxlog, .txt
    </div>
</div>

<div class="file-selected" id="fileSelected">
    <div id="fileName"></div>
    <div id="fileSize"></div>
    <i class="fe fe-check-circle"></i>
</div>
```

---

## ✨ Summary

The blade file is **already set up** to:

✅ Display auto-filled data from $autoFillData array
✅ Show "Auto-filled from task" indicators
✅ Make fields read-only when task is provided
✅ Display task context information
✅ Handle file upload with drag & drop
✅ Validate form inputs
✅ Submit to /process-log route
✅ Pass task_id as hidden field

**No major changes needed** - just the visual indicators added!

---

**Current Status:** ✅ Blade file ready to use
