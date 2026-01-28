# MPFM System Architecture - Visual Summary

## 🏗️ Current System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        HTTP Layer                               │
│  (18 Controllers, Routes, Requests, Resources)                 │
└────────────────┬────────────────────────────────────────────────┘
                 │
┌────────────────┴────────────────────────────────────────────────┐
│                    Service Layer                                │
│  (27 Services - some with overlapping concerns)                │
│                                                                  │
│  ❌ Problem: Fragmented, circular dependencies                  │
│  ❌ 3 MPFM calculation services instead of 1                   │
│  ❌ No explicit workflow state machine                          │
│  ❌ No lift curve analytics                                     │
└────────────────┬────────────────────────────────────────────────┘
                 │
┌────────────────┴────────────────────────────────────────────────┐
│                      Model Layer                                │
│  (24 Models with mixed responsibilities)                       │
│                                                                  │
│  ❌ Business logic in models                                    │
│  ❌ State management scattered                                  │
│  ❌ Task duplication: PlanTask vs DailyPlan vs OperatorPlan   │
└────────────────┬────────────────────────────────────────────────┘
                 │
┌────────────────┴────────────────────────────────────────────────┐
│                   Database Layer                                │
│  (26+ Tables, some denormalized)                               │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🎯 Proposed Domain-Driven Architecture

```
┌──────────────────────────────────────────────────────────────────┐
│                    APPLICATION LAYER                             │
│     (HTTP Controllers, API, Events, Notifications)              │
└─────────────────────────┬──────────────────────────────────────┘
                          │
┌─────────────────────────┴──────────────────────────────────────┐
│                  ORCHESTRATION LAYER                            │
│    (Workflow Engine, Task Coordinator, Dashboard Aggregator)   │
└─────────────────────────┬──────────────────────────────────────┘
                          │
┌─────────────────────────┴──────────────────────────────────────┐
│                   DOMAIN SERVICE LAYER                          │
│                                                                  │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────┐ │
│  │ Task Workflow    │  │ MPFM Processing  │  │Lift Analysis │ │
│  │ Domain           │  │ Domain           │  │Domain        │ │
│  │                  │  │                  │  │              │ │
│  │✓ StateMachine    │  │✓ LogProcessor    │  │✓ Calculator  │ │
│  │✓ WorkflowEngine  │  │✓ Calculator      │  │✓ Trending    │ │
│  │✓ SLAMonitor      │  │✓ Validator       │  │✓ Prediction  │ │
│  │✓ AuthPolicy      │  │✓ Comparison      │  │✓ Integration │ │
│  └──────────────────┘  └──────────────────┘  └──────────────┘ │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │    Organization & Operational Services                  │  │
│  │  • Department Configuration    • Analytics              │  │
│  │  • User/Role Management        • Reporting              │  │
│  │  • Authorization               • Charting               │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────┬──────────────────────────────────────┘
                          │
┌─────────────────────────┴──────────────────────────────────────┐
│                    DOMAIN MODEL LAYER                           │
│                                                                  │
│  Task Domain   │  MPFM Domain   │  Lift Curve Domain            │
│  ─────────────   ────────────   ─────────────────               │
│  Task          │  MPFMTest      │  LiftCurve                    │
│  StateHistory  │  TestMetric    │  CurvePoint                   │
│  SLAHistory    │  Validation    │  Trend                        │
│  Config        │  Comparison    │  Prediction                   │
│                │  Rules         │  Metadata                     │
└────────────────────────────────────────────────────────────────┘
```

---

## 5️⃣ Five Distinct Domains

### 1. **Task Workflow Domain** 🔄
**Purpose:** Orchestrate task lifecycle from creation to completion

**Entities:**
- Task (unified - replaces PlanTask, DailyPlan, OperatorPlan)
- TaskStateHistory (audit trail)
- TaskSLAHistory (SLA tracking)
- TaskConfiguration (workflow settings)

**State Machine:**
```
DRAFT
  ↓
SUBMITTED_TO_SURVEILLANCE
  ↓
ASSIGNED_TO_DEPARTMENT
  ↓
IN_PROGRESS
  ↓
DEPARTMENT_REVIEWED
  ↓
SURVEILLANCE_REVIEWED
  ↓
JOB_OFFICER_APPROVED
  ↓
COMPLETED
```

**Services:**
- TaskStateMachine (state transitions)
- TaskWorkflowEngine (orchestration)
- TaskSLAMonitor (SLA enforcement)
- TaskAuthorizationPolicy (permissions)

---

### 2. **MPFM Testing Domain** 🔬
**Purpose:** Manage multiphase flow meter tests

**Entities:**
- MPFMTest (core test data)
- MPFMTestMetric (50+ typed metrics)
- MPFMValidationRule (rules)
- MPFMTestValidation (validation results)
- MPFMTestComparison (historical comparison)

**Services:**
- MPFMLogProcessor (VX file parsing)
- MPFMCalculationEngine (unified calculation)
- MPFMValidator (rule validation)
- MPFMComparison (trend analysis)
- MPFMDataQualityAssessor (quality assessment)

**Key Feature:** Consolidated 3 services into 1 unified engine

---

### 3. **Lift Curve Analysis Domain** 📈
**Purpose:** Generate lift curves and performance predictions

**Entities:**
- LiftCurve (master curve)
- LiftCurvePoint (individual points)
- PerformanceTrend (trend analysis)
- PerformancePrediction (forecast)
- LiftCurveMetadata (calculation versioning)

**Multi-Source Data Integration:**
```
MPFM Tests      → Oil/gas rates, pressures, temps
Production Ops  → Daily rates, choke positions
Wireline        → Downhole pressures, fluid levels
Completion Data → Equipment specs, tubing size
Maintenance     → Equipment changes, workover dates
         ↓
      Normalize
         ↓
      Filter (steady-state only)
         ↓
      Remove outliers
         ↓
      Calculate drawdown
         ↓
      Fit curve (polynomial or exponential)
         ↓
      Assess trend vs baseline
         ↓
   LIFT CURVE GENERATED
```

**Services:**
- LiftCurveCalculationService (core algorithm)
- LiftCurveGenerationService (orchestration)
- PerformanceTrendAnalyzer (trend detection)
- PerformancePredictionService (forecasting)
- LiftCurveDataIntegrator (multi-source aggregation)

---

### 4. **Organization Domain** 👥
**Purpose:** Users, departments, roles, permissions

**Entities:**
- User (enhanced with metadata)
- Department (org structure)
- DepartmentConfiguration (JSON: workflows, KPIs, SLAs)
- Role (via Spatie)
- Permission (via Spatie)
- DepartmentRole (role assignments)

**Services:**
- DepartmentConfigurationService
- DepartmentWorkflowStrategyFactory
- PermissionAuthorizationService
- UserAccessValidator

**Key Feature:** Department-specific configurations enable customization

---

### 5. **Well Assets Domain** 🏭
**Purpose:** Oil fields, towers, strings, geometry

**Entities:**
- OilField
- Tower
- OilString
- WellGeometry (NEW: tubing size, equipment)
- EquipmentHistory (NEW: maintenance tracking)

**Services:**
- AssetHierarchyService
- AssetAvailabilityService
- EquipmentHistoryService

---

## 📊 Data Flow Examples

### Task Creation & Completion Workflow
```
1. Job Officer creates Task (DRAFT)
   → Type-specific data stored in JSON column
   → StateHistory recorded

2. Submits to Surveillance (SUBMITTED_TO_SURVEILLANCE)
   → TaskSLAService calculates SLA deadline
   → Event fired: TaskStateTransitioned

3. Surveillance assigns to Department
   → ASSIGNED_TO_DEPARTMENT
   → Operator assigned via many-to-many

4. Operator executes task
   → Transitions to IN_PROGRESS
   → Uploads results/MPFM log

5. Department Supervisor reviews
   → DEPARTMENT_REVIEWED
   → Can approve or reject

6. Surveillance Supervisor final check
   → SURVEILLANCE_REVIEWED
   → Can approve or reject

7. Job Officer approves
   → JOB_OFFICER_APPROVED

8. Task completion
   → COMPLETED
   → actual_completion_at recorded
   → SLA tracked (on-time? overdue?)
```

### MPFM Test to Lift Curve Pipeline
```
VX Log File Uploaded
      ↓
MPFMLogProcessor
  - Parse VX format
  - Extract raw data
      ↓
MPFMCalculationEngine
  - PVT calculations
  - Flow rate metrics
  - Ratio calculations
  - Statistical metrics
      ↓
MPFMTestMetric records
  (50+ typed metrics)
      ↓
MPFMValidator
  - Run validation rules
  - Assess data quality
      ↓
IF quality ≥ 75% THEN
      ↓
LiftCurveDataIntegrator
  - Collect MPFM tests
  - Aggregate production rates
  - Include wireline data
  - Normalize to reference conditions
      ↓
LiftCurveCalculationService
  - Filter steady-state data
  - Remove outliers
  - Calculate pressure drawdown
  - Fit polynomial curve
      ↓
LiftCurve Generated
  - Stored in database
  - Trend vs baseline
  - Prediction generated
```

---

## 🗂️ Folder Structure

```
app/Domains/
├── Task/
│   ├── Models/ (Task, StateHistory, SLAHistory, Config)
│   ├── Services/ (StateMachine, WorkflowEngine, SLAMonitor)
│   ├── Enums/ (TaskState, TaskType, TaskPriority)
│   └── Events/
├── MPFM/
│   ├── Models/ (Test, Metric, Validation, Comparison)
│   ├── Services/ (LogProcessor, CalculationEngine, Validator)
│   ├── Calculators/ (PVT, FlowRate, Ratio, Cumulative)
│   └── Events/
├── LiftAnalysis/
│   ├── Models/ (Curve, Point, Trend, Prediction)
│   ├── Services/ (CalculationService, Generation, Trending)
│   ├── Calculators/ (Drawdown, Stability, Outlier)
│   └── Events/
├── Organization/
│   ├── Models/ (User, Department, Config, Role)
│   ├── Services/ (ConfigService, StrategyFactory)
│   └── Policies/
├── WellAssets/
│   ├── Models/ (OilField, Tower, String, Geometry)
│   └── Services/
└── Shared/
    ├── Enums/
    ├── ValueObjects/ (Measurement, Pressure, etc.)
    └── Exceptions/
```

---

## 🚀 Implementation Roadmap (6 Months)

```
Month 1-2: Foundation
├── Task model unification
├── State machine service
├── Database migrations (phase 1-2)
└── Unit tests

Month 2-3: MPFM Domain
├── Consolidate services (3 → 1)
├── Calculation engine
├── Metric models
└── Integration tests

Month 3-4: Lift Analysis
├── LiftCurve models
├── Algorithm implementation
├── Trend analysis
└── Integration tests

Month 4-5: Dashboard & UI
├── Department dashboards
├── Lift curve visualization
├── Task workflow UI
└── Reporting

Month 5-6: Analytics & Production
├── Predictions
├── Alerts
├── Optimization
└── Deploy to production
```

---

## ✅ Key Benefits of Refactoring

### Before ❌
- 27 services with overlapping concerns
- 3 MPFM calculation services doing similar work
- Task logic split across PlanTask, DailyPlan, OperatorPlan
- No lift curve analytics
- State management scattered across controllers
- Hard to test, maintain, extend

### After ✅
- 5 distinct domains with clear boundaries
- 1 unified MPFM calculation engine
- 1 unified Task model with polymorphic JSON storage
- Lift curve analytics as first-class feature
- Explicit state machine in dedicated service
- Easy to test (services, calculators), maintain (clear separation), extend (add domain, add calculator)

---

## 🔒 Non-Breaking Refactoring

**Zero Data Loss:**
- All existing data preserved
- Phased migration (6 weeks)
- Parallel processing during cutover
- Rollback plan available

**Backward Compatibility:**
- Old endpoints continue working
- New controllers alongside old (feature flags)
- Models updated transparently
- Read-only archive of old tables

**Deployment Strategy:**
1. Add new tables (non-breaking)
2. Migrate data in staging
3. Deploy new code (both systems running)
4. Switch traffic gradually
5. Keep old system as fallback

---

## 📈 Scalability Features

✅ **Unlimited Departments** - No hardcoded limits
✅ **Unlimited Tasks** - Proper indexing for performance
✅ **Unlimited Users** - Distributed role checking
✅ **Unlimited Data Points** - Lift curve with 1000+ points
✅ **Multi-Source Data** - Aggregates from 5+ sources
✅ **Concurrent Access** - Transaction support, locking
✅ **High-Volume Analytics** - Batch processing, caching

---

## 🎓 Next Steps for Development Team

1. **Review Architecture**
   - Read ARCHITECTURE_REFACTORING_PLAN.md
   - Discuss domain boundaries
   - Validate assumptions

2. **Plan Implementation**
   - Set timeline
   - Allocate resources
   - Establish testing protocols

3. **Begin Phase 1**
   - Task model refactoring
   - State machine service
   - Database backups & prep

4. **Establish Testing**
   - Unit test suite
   - Integration tests
   - Performance benchmarks

5. **Deploy Gradually**
   - Shadow mode (old + new)
   - Parallel processing
   - Performance monitoring

---

**Document Version:** 1.0
**Status:** Ready for Review & Implementation
**Contact:** Development Team
