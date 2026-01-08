# Frontend Blade Views Refactoring Plan
## Enterprise MPFM System - ADNOC Level

**Date**: 2026-01-06
**Status**: In Progress
**Theme**: Valex Admin Bootstrap Theme

---

## 📋 Current Analysis

### ✅ What's Already Good:
1. ✅ Valex theme properly integrated
2. ✅ Master layout structure clean (`layouts/master.blade.php`)
3. ✅ Oil Fields index with DataTables AJAX
4. ✅ Authorization directives (`@can/@cannot`) present
5. ✅ Dashboard with basic KPIs and charts
6. ✅ Consistent Valex card structure

### 🔴 What Needs Refactoring:
1. ❌ Missing enhanced role-based dashboard content
2. ❌ Incomplete CRUD forms (create/edit) for all modules
3. ❌ Reports views need KPI enhancement
4. ❌ MPFM Tests views need DataTables integration
5. ❌ Missing form partials (`_form.blade.php`)
6. ❌ Dynamic dropdowns not fully implemented
7. ❌ Operator Plans views incomplete
8. ❌ Well Tests comparison views need enhancement

---

## 🎯 Refactoring Scope

### 1️⃣ **Oil Fields Module** ✅ (Already Good)
**Files**:
- ✅ `resources/views/oil_fields/index.blade.php` - Has DataTables
- ⏳ `resources/views/oil_fields/create.blade.php` - Needs form
- ⏳ `resources/views/oil_fields/edit.blade.php` - Needs form
- ⏳ `resources/views/oil_fields/show.blade.php` - Needs details
- 🆕 `resources/views/oil_fields/_form.blade.php` - Need to create

**Tasks**:
- [x] Index with DataTables AJAX
- [ ] Create form with validation
- [ ] Edit form with validation
- [ ] Show view with towers list
- [ ] Form partial for reusability

---

### 2️⃣ **Towers Module**
**Files**:
- ⏳ `resources/views/towers/index.blade.php` - Need DataTables
- ⏳ `resources/views/towers/create.blade.php` - Need form
- ⏳ `resources/views/towers/edit.blade.php` - Need form
- ⏳ `resources/views/towers/show.blade.php` - Need details
- 🆕 `resources/views/towers/_form.blade.php` - Need to create

**Tasks**:
- [ ] Index with DataTables AJAX
- [ ] Dynamic Oil Field dropdown
- [ ] Create form with validation
- [ ] Edit form with validation
- [ ] Show view with oil strings list
- [ ] Form partial for reusability

---

### 3️⃣ **Oil Strings Module**
**Files**:
- ⏳ `resources/views/oil_strings/index.blade.php` - Need DataTables
- ⏳ `resources/views/oil_strings/create.blade.php` - Need form
- ⏳ `resources/views/oil_strings/edit.blade.php` - Need form
- ⏳ `resources/views/oil_strings/show.blade.php` - Need details
- 🆕 `resources/views/oil_strings/_form.blade.php` - Need to create

**Tasks**:
- [ ] Index with DataTables AJAX
- [ ] Cascading dropdowns (Oil Field → Tower)
- [ ] Create form with validation
- [ ] Edit form with validation
- [ ] Show view with tests list
- [ ] Form partial for reusability

---

### 4️⃣ **MPFM Operational Plans Module**
**Files**:
- ⏳ `resources/views/mpfm_operational_plans/index.blade.php` - Need DataTables
- ⏳ `resources/views/mpfm_operational_plans/create.blade.php` - Need form
- ⏳ `resources/views/mpfm_operational_plans/edit.blade.php` - Need form
- ⏳ `resources/views/mpfm_operational_plans/show.blade.php` - Need details
- 🆕 `resources/views/mpfm_operational_plans/_form.blade.php` - Need to create

**Tasks**:
- [ ] Index with DataTables AJAX + filters
- [ ] Status badges (draft/submitted/approved)
- [ ] Cascading dropdowns
- [ ] Create form with all fields
- [ ] Edit form with status workflow
- [ ] Show view with approval buttons
- [ ] Form partial for reusability

---

### 5️⃣ **MPFM Tests Module**
**Files**:
- ⏳ `resources/views/mpfm_tests/index.blade.php` - Need DataTables
- ✅ `resources/views/mpfm_tests/upload.blade.php` - Already good
- ✅ `resources/views/mpfm_tests/preview.blade.php` - Already comprehensive
- ⏳ `resources/views/mpfm_tests/show.blade.php` - Need enhancement
- ⏳ `resources/views/mpfm_tests/edit.blade.php` - Need form

**Tasks**:
- [ ] Index with DataTables AJAX + filters
- [ ] Upload form (already good)
- [ ] Preview (already comprehensive - 1145 lines!)
- [ ] Show view with charts and KPIs
- [ ] Edit metadata form
- [ ] Delete confirmation

---

### 6️⃣ **Operator Plans Module**
**Files**:
- ⏳ `resources/views/operator_plans/index.blade.php` - Need enhancement
- ⏳ `resources/views/operator_plans/create.blade.php` - Need form
- ⏳ `resources/views/operator_plans/edit.blade.php` - Need form
- 🆕 `resources/views/operator_plans/_form.blade.php` - Need to create

**Tasks**:
- [ ] Index with step tracking
- [ ] Create plan with steps
- [ ] Edit plan with reordering
- [ ] Mark steps as complete (AJAX)
- [ ] Progress indicators
- [ ] Form partial for reusability

---

### 7️⃣ **Reports Module**
**Files**:
- ⏳ `resources/views/reports/job_officer.blade.php`
- ⏳ `resources/views/reports/reservoir_engineer.blade.php`
- ⏳ `resources/views/reports/production_supervisor.blade.php`
- ⏳ `resources/views/reports/mpfm_supervisor.blade.php`
- ⏳ `resources/views/reports/mpfm_operator.blade.php`

**Tasks**:
- [ ] KPI cards for each role
- [ ] DataTables for test lists
- [ ] Date range filters
- [ ] Export buttons (PDF/Excel)
- [ ] Charts and visualizations
- [ ] Role-specific content

---

### 8️⃣ **Dashboard Enhancement**
**File**: `resources/views/dashboard.blade.php`

**Current**: ✅ Basic KPIs, charts, quick actions
**Needs**:
- [ ] Enhanced role-based KPIs
- [ ] Production metrics (oil rate, gas rate, GOR)
- [ ] Recent tests widget
- [ ] Active operators widget
- [ ] System alerts widget
- [ ] Today's schedule widget

---

## 🎨 Valex Theme Standards

### Card Structure:
```blade
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Title</h3>
        <div class="card-options">
            <!-- Actions -->
        </div>
    </div>
    <div class="card-body">
        <!-- Content -->
    </div>
</div>
```

### DataTables Template:
```blade
@push('styles')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

<table class="table table-bordered table-striped" id="tableName">
    <thead>
        <tr>
            <th>Column 1</th>
            <th>Column 2</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

@push('scripts')
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
$('#tableName').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route('...') }}',
    columns: [...]
});
</script>
@endpush
```

### Status Badges:
```blade
@if($status == 'active')
    <span class="badge badge-success">Active</span>
@elseif($status == 'inactive')
    <span class="badge badge-secondary">Inactive</span>
@elseif($status == 'draft')
    <span class="badge badge-warning">Draft</span>
@elseif($status == 'submitted')
    <span class="badge badge-info">Submitted</span>
@elseif($status == 'approved')
    <span class="badge badge-success">Approved</span>
@endif
```

### Action Buttons:
```blade
@can('view', $model)
    <a href="{{ route('...show', $model) }}" class="btn btn-sm btn-info">
        <i class="fe fe-eye"></i>
    </a>
@endcan

@can('update', $model)
    <a href="{{ route('...edit', $model) }}" class="btn btn-sm btn-primary">
        <i class="fe fe-edit"></i>
    </a>
@endcan

@can('delete', $model)
    <form action="{{ route('...destroy', $model) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
            <i class="fe fe-trash-2"></i>
        </button>
    </form>
@endcan
```

---

## 📊 DataTables Integration

### Backend Requirements:
Each controller must have a DataTables method like:
```php
public function datatables()
{
    return datatables()
        ->eloquent(Model::query())
        ->addColumn('action', function ($model) {
            return view('partials.actions', compact('model'));
        })
        ->rawColumns(['action', 'status_badge'])
        ->make(true);
}
```

### Frontend Implementation:
```javascript
$('#tableName').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route('model.datatables') }}',
    columns: [
        { data: 'id', name: 'id' },
        { data: 'name', name: 'name' },
        { data: 'status_badge', name: 'status', orderable: false },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ],
    order: [[0, 'desc']],
    pageLength: 25
});
```

---

## 🔐 Authorization Patterns

### In Views:
```blade
@can('create', App\Models\ModelName::class)
    <a href="{{ route('model.create') }}" class="btn btn-primary">
        <i class="fe fe-plus"></i> Create New
    </a>
@endcan

@can('update', $model)
    <!-- Edit button -->
@endcan

@can('delete', $model)
    <!-- Delete button -->
@endcan

@role('admin')
    <!-- Admin-only content -->
@endrole

@hasanyrole('supervisor|admin')
    <!-- Supervisor or admin content -->
@endhasanyrole
```

---

## 🔄 Dynamic Dropdowns

### Oil Field → Towers:
```blade
<select id="oil_field_id" name="oil_field_id" class="form-control">
    <option value="">Select Oil Field</option>
    @foreach($oilFields as $field)
        <option value="{{ $field->id }}">{{ $field->name }}</option>
    @endforeach
</select>

<select id="tower_id" name="tower_id" class="form-control">
    <option value="">Select Tower</option>
</select>

<script>
$('#oil_field_id').change(function() {
    var oilFieldId = $(this).val();
    if(oilFieldId) {
        $.ajax({
            url: '/ajax/towers/' + oilFieldId,
            type: 'GET',
            success: function(data) {
                $('#tower_id').empty().append('<option value="">Select Tower</option>');
                $.each(data, function(key, tower) {
                    $('#tower_id').append('<option value="'+ tower.id +'">'+ tower.name +'</option>');
                });
            }
        });
    }
});
</script>
```

---

## 📝 Form Validation

### Display Errors:
```blade
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@error('field_name')
    <span class="text-danger">{{ $message }}</span>
@enderror
```

### Success Messages:
```blade
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('success') }}
    </div>
@endif
```

---

## 🎯 Priority Order

### Phase 1 (Critical):
1. ✅ Dashboard role-based KPIs
2. ✅ Oil Fields (already done)
3. ✅ Towers (with dropdowns)
4. ✅ Oil Strings (with cascading dropdowns)

### Phase 2 (High Priority):
5. ✅ MPFM Operational Plans
6. ✅ MPFM Tests index
7. ✅ Operator Plans

### Phase 3 (Medium Priority):
8. ✅ Reports (all roles)
9. ✅ Form partials
10. ✅ Enhanced show views

---

## ✅ Checklist Per Module

For each module, ensure:
- [ ] Index has DataTables AJAX
- [ ] Create form has validation
- [ ] Edit form pre-fills data
- [ ] Show view displays all info
- [ ] Authorization directives present
- [ ] Status badges used
- [ ] Actions have icons
- [ ] Forms have CSRF tokens
- [ ] Success/error messages displayed
- [ ] Valex card structure followed
- [ ] Responsive design
- [ ] Form partial created (if applicable)

---

## 🚀 Next Steps

1. Start with enhancing Dashboard
2. Complete Oil Fields CRUD
3. Build Towers with dropdowns
4. Build Oil Strings with cascading dropdowns
5. Build MPFM Operational Plans
6. Enhance MPFM Tests
7. Build Operator Plans
8. Enhance Reports

---

**Status**: Ready to proceed with implementation!
