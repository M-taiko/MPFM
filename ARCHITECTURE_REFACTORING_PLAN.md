# MPFM System - Enterprise Architecture Refactoring Plan
## Principal Software Architect Analysis & Implementation Strategy

---

## EXECUTIVE SUMMARY

This document outlines a **phased, production-safe refactoring** of the MPFM oil & gas operations system. The refactor consolidates domain logic, introduces explicit workflow patterns, and enables **Lift Curve analytics** as a first-class feature.

**Key Objectives:**
- ✅ No breaking changes
- ✅ Incremental implementation
- ✅ Unified task engine (eliminate duplication)
- ✅ Explicit workflow state machine
- ✅ Modular domain architecture
- ✅ Lift Curve analytics foundation

---

## PART 1: CURRENT STATE ANALYSIS

### 1.1 ARCHITECTURAL SMELLS (Issues to Address)

#### ❌ SMELL #1: Task Model Fragmentation
**Problem:**
- `PlanTask`, `DailyPlan`, `OperatorPlan` are semi-duplicated task concepts
- Task logic scattered across 12+ controllers
- State management logic mixed with business logic

**Impact:**
- Hard to maintain unified workflow
- Inconsistent SLA enforcement
- Data inconsistency between task types

**Refactor Approach:**
→ Unify into single **Task** entity with `task_type` enum
→ Move state logic into dedicated `TaskStateMachine` service
→ Use polymorphic inheritance via JSON column for type-specific data

---

#### ❌ SMELL #2: Service Layer Explosion
**Problem:**
- 27+ services with overlapping responsibilities
- `MPFMCalculationService` + `MPFMDetailedMetricsExtractorService` + `MPFMLogProcessingService` (3 calculation layers)
- No clear separation between MPFM domain and business orchestration

**Impact:**
- Difficult to test
- Circular dependencies possible
- Hard to reason about data flow

**Refactor Approach:**
→ Consolidate into 4 **Domain Service Clusters**:
  1. **MPFM Processing** (one unified pipeline)
  2. **Task Workflow** (unified state handling)
  3. **Analytics/Lift Curve** (calculation engine)
  4. **Operational** (reporting, dashboards, utilities)

---

#### ❌ SMELL #3: Missing Lift Curve First-Class Domain
**Problem:**
- No `LiftCurve` or `LiftCurvePoint` models
- Lift curve logic doesn't exist explicitly
- Data (MPFM, Production, Wireline) scattered across incompatible structures

**Impact:**
- Cannot generate lift curves
- Cannot track performance trends
- Cannot enable production surveillance

**Refactor Approach:**
→ Create dedicated `Lift Analysis` domain
→ Build `LiftCurveCalculationService` with explicit algorithm
→ Normalize multi-source data (MPFM, production rates, wireline)

---

#### ❌ SMELL #4: Workflow State Logic Coupled to Controllers
**Problem:**
- State transitions embedded in 5+ controllers (`TaskStateController`, `MonthlyPlanController`, supervisors, etc.)
- Authorization checks duplicated
- Business logic mixed with HTTP concerns

**Impact:**
- Hard to test state machine
- Authorization easily bypassed
- Business rules not enforced consistently

**Refactor Approach:**
→ Extract `TaskWorkflowEngine` service
→ Create `WorkflowPolicies` class for authorization rules
→ Implement explicit state machine with allowed transitions

---

#### ❌ SMELL #5: Department-Specific Logic Hard-Coded
**Problem:**
- Departments are just foreign keys on tasks
- No department-specific workflows or configurations
- Dashboard/KPI logic per-role, not per-department

**Impact:**
- Cannot easily add new departments
- MPFM-specific behavior couples to generic task model
- Scaling to N departments difficult

**Refactor Approach:**
→ Create `DepartmentConfiguration` entity (JSON-driven)
→ Implement `DepartmentWorkflowStrategy` pattern
→ Build department-specific dashboard services

---

#### ❌ SMELL #6: MPFM Data Structure Too Flat
**Problem:**
- 50+ metrics in JSON columns (untyped)
- No versioning for calculation method changes
- Validation rules scattered across services

**Impact:**
- Cannot debug metric derivations
- Hard to audit when calculation changes
- Data quality unclear

**Refactor Approach:**
→ Create explicit `MPFM Metrics` domain models
→ Introduce `MetricVersioning` (track calculation method)
→ Normalize metrics into typed columns/relationships

---

### 1.2 Current Complexity Map

```
Controllers (18 major ones)
    ↓
Services (27 specialized)
    ↓
Models (24 models, many with overlapping concerns)
    ↓
Database (26+ tables, some denormalized)
    ↓
Enums (TaskState: 8 states)
```

**Current Dependencies:**
- Controllers → Services → Models → Database
- Services → Services (circular dependencies possible)
- Models contain business logic (violates thin model principle)
- No explicit domain boundaries

---

## PART 2: PROPOSED ARCHITECTURE

### 2.1 High-Level Domain Architecture

```
┌──────────────────────────────────────────────────────────────────┐
│                          APPLICATION LAYER                       │
│  (HTTP Controllers, API Endpoints, Events, Notifications)        │
└────────────────────────────┬─────────────────────────────────────┘
                             │
┌────────────────────────────┴─────────────────────────────────────┐
│                       ORCHESTRATION LAYER                         │
│  (Workflow Engine, Task Coordinator, Dashboard Aggregator)       │
└────────────────────────────┬─────────────────────────────────────┘
                             │
┌────────────────────────────┴─────────────────────────────────────┐
│                      DOMAIN SERVICE LAYER                         │
│                                                                   │
│  ┌─────────────────┐  ┌──────────────────┐  ┌──────────────┐   │
│  │  Task Workflow  │  │  MPFM Processing │  │ Lift Analysis │  │
│  │   Domain        │  │   Domain         │  │  Domain      │   │
│  │                 │  │                  │  │              │   │
│  │ • Validation    │  │ • Log Parsing    │  │ • Calculation│   │
│  │ • Transitions   │  │ • Calculation    │  │ • Trending   │   │
│  │ • SLA           │  │ • Validation     │  │ • Performance│   │
│  │ • History       │  │ • Caching        │  │ • Prediction │   │
│  └─────────────────┘  └──────────────────┘  └──────────────┘   │
│                                                                   │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │    Organization & Operational Services                    │ │
│  │                                                            │ │
│  │  • Department Configuration    • Analytics               │ │
│  │  • User/Role Management        • Reporting               │ │
│  │  • Permission Authorization    • Charting                │ │
│  └────────────────────────────────────────────────────────────┘ │
└────────────────────────────┬─────────────────────────────────────┘
                             │
┌────────────────────────────┴─────────────────────────────────────┐
│                         DOMAIN MODEL LAYER                        │
│                                                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────────┐  │
│  │ Task Workflow│  │ MPFM Testing │  │  Lift Curve Analysis │  │
│  │              │  │              │  │                      │  │
│  │ • Task       │  │ • MPFMTest   │  │ • LiftCurve         │  │
│  │ • StateHist  │  │ • Validation │  │ • LiftCurvePoint    │  │
│  │ • SLAHist    │  │ • Comparison │  │ • PerformanceTrend  │  │
│  │ • TaskConfig │  │ • Rules      │  │ • Prediction        │  │
│  └──────────────┘  └──────────────┘  └──────────────────────┘  │
│                                                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────────┐  │
│  │ Organization │  │  Well/Assets │  │  Supporting Models   │  │
│  │              │  │              │  │                      │  │
│  │ • User       │  │ • OilField   │  │ • Department Config  │  │
│  │ • Department │  │ • Tower      │  │ • Metric Version     │  │
│  │ • Role       │  │ • OilString  │  │ • Quality Metrics    │  │
│  └──────────────┘  └──────────────┘  └──────────────────────┘  │
└────────────────────────────┬─────────────────────────────────────┘
                             │
┌────────────────────────────┴─────────────────────────────────────┐
│                       PERSISTENCE LAYER                           │
│                                                                   │
│  • Eloquent Models (ORM)    • Query Repositories                │
│  • Database Migrations      • Caching Layer                     │
│  • Indexes & Optimization   • Event Listeners                   │
└──────────────────────────────────────────────────────────────────┘
```

---

### 2.2 Domain Definitions & Boundaries

#### **DOMAIN 1: Task Workflow Management**
**Purpose:** Orchestrate task lifecycle from creation to completion

**Entities:**
- `Task` (unified, replaces PlanTask/DailyPlan/OperatorPlan)
- `TaskStateHistory` (audit trail)
- `TaskSLAHistory` (SLA tracking)
- `TaskConfiguration` (workflow-specific settings)

**Services:**
- `TaskStateMachine` - State transitions with validation
- `TaskWorkflowEngine` - Orchestrate multi-step workflows
- `TaskSLAMonitor` - SLA tracking and escalation
- `TaskAuthorizationPolicy` - Role-based access control

**External Dependencies:** Organization domain (users, roles)

**Data Flow:**
```
Task Created (DRAFT)
  → Submitted (SUBMITTED_TO_SURVEILLANCE)
  → Assigned (ASSIGNED_TO_DEPARTMENT)
  → Started (IN_PROGRESS)
  → Reviewed (DEPARTMENT_REVIEWED)
  → Approved (JOB_OFFICER_APPROVED)
  → Completed (COMPLETED)

At each step: StateHistory recorded, SLA tracked, Notifications sent
```

---

#### **DOMAIN 2: MPFM Testing & Validation**
**Purpose:** Manage multiphase flow meter test lifecycle

**Entities:**
- `MPFMTest` (core test data)
- `MPFMTestMetric` (typed metrics, replaces JSON)
- `MPFMValidationRule` (validation rules)
- `MPFMTestValidation` (validation results)
- `MPFMTestComparison` (trend comparison)
- `MPFMMetricVersion` (calculation method versioning)

**Services:**
- `MPFMLogProcessor` - Parse VX log files → structured data
- `MPFMCalculationEngine` - Compute all metrics (unified from 3 services)
- `MPFMValidator` - Run validation rules against tests
- `MPFMComparison` - Compare tests with historical data
- `MPFMDataQualityAssessor` - Assess data quality/fitness
- `MPFMHistoricalTrendAnalyzer` - Trend analysis

**External Dependencies:** Well Assets domain, Lift Analysis domain

**Data Flow:**
```
VX Log File Uploaded
  → Parse into structured format (MPFMLogProcessor)
  → Calculate metrics (MPFMCalculationEngine)
  → Store as MPFMTest + MPFMTestMetrics
  → Validate against rules (MPFMValidator)
  → Compare with historical (MPFMComparison)
  → Assess quality (MPFMDataQualityAssessor)
  → Trigger Lift Curve update (if linked to task)
```

---

#### **DOMAIN 3: Lift Curve Analytics**
**Purpose:** Calculate, trend, and predict production performance using lift curves

**Entities:**
- `LiftCurve` (master curve data)
- `LiftCurvePoint` (individual curve points)
- `PerformanceTrend` (performance over time)
- `PerformancePrediction` (projected future behavior)
- `LiftCurveMetadata` (calculation method, versioning)

**Services:**
- `LiftCurveCalculationService` - Core lift curve algorithm
- `LiftCurveGenerationService` - Generate curves from multi-source data
- `PerformanceTrendAnalyzer` - Trend calculations
- `PerformancePredictionService` - Predictive analytics
- `LiftCurveDataIntegrator` - Aggregate data from MPFM, Production, Wireline

**Input Data Sources:**
1. **MPFM Tests** → Oil rate, gas rate, water cut, pressure, temperature
2. **Production Operations** → Daily rates, choke positions, well head pressure
3. **Wireline Services** → Downhole pressure, fluid levels, mechanical condition
4. **Completion Data** → Tubing size, equipment, restrictions
5. **Maintenance Events** → Workover dates, equipment changes, cleanouts

**Algorithm Overview:**
```
INPUT: Multi-source data (MPFM, Production, Wireline)
  ↓
NORMALIZE: Convert to common units and reference conditions
  ↓
FILTER: Remove outliers, equipment-off periods, invalid data
  ↓
STABILIZATION CHECK: Ensure steady-state data
  ↓
CURVE GENERATION: Plot Qo vs ΔP (pressure drawdown)
  ↓
TREND ANALYSIS: Compare current curve vs baseline
  ↓
IMPACT ASSESSMENT: Identify performance changes
  ↓
OUTPUT: LiftCurve, PerformanceTrend, PerformancePrediction
```

**External Dependencies:** MPFM domain, Well Assets domain, Production domain

---

#### **DOMAIN 4: Organization & Access Control**
**Purpose:** Manage users, departments, roles, and permissions

**Entities:**
- `User` (enhanced with department-specific metadata)
- `Department` (multi-level departments supported)
- `DepartmentConfiguration` (JSON: workflow rules, KPIs, access)
- `Role` (via Spatie)
- `Permission` (via Spatie)
- `DepartmentRole` (department-specific role assignments)

**Services:**
- `DepartmentConfigurationService` - Manage department settings
- `DepartmentWorkflowStrategyFactory` - Create department-specific workflows
- `PermissionAuthorizationService` - Unified permission checking
- `UserAccessValidator` - Cross-department access validation

**Department Configurations:**
```json
{
  "id": 1,
  "name": "MPFM Operations",
  "workflow_strategy": "mpfm_standard",
  "sla_durations": {
    "draft": 1,
    "surveillance": 2,
    "assigned": 5,
    "in_progress": 10
  },
  "approval_hierarchy": ["job_officer", "surveillance_supervisor", "mpfm_supervisor"],
  "kpi_definitions": ["test_count", "pass_rate", "average_quality"],
  "data_retention_days": 2555,
  "requires_validation": true
}
```

**External Dependencies:** Authorization/Authentication infrastructure

---

#### **DOMAIN 5: Well Assets & Infrastructure**
**Purpose:** Manage oil fields, towers, oil strings, and well geometry

**Entities:**
- `OilField`
- `Tower`
- `OilString`
- `WellGeometry` (new: completion data, equipment)
- `EquipmentHistory` (new: equipment changes, maintenance)

**Services:**
- `AssetHierarchyService` - Navigate field → tower → string hierarchy
- `AssetAvailabilityService` - Determine which assets are available for testing
- `EquipmentHistoryService` - Track equipment changes and their impact

**External Dependencies:** Maintenance domain (Workover services)

---

### 2.3 Conceptual Entity-Relationship Diagram (ERD)

```
┌─────────────────────────────────────────────────────────────────┐
│                        ORGANIZATION DOMAIN                       │
│                                                                   │
│   User ◄──── Department ────┐                                    │
│    │        (has many)      │                                    │
│    │                        ├──► DepartmentConfiguration (JSON)  │
│    │                        │                                    │
│    └──► Role ◄──► Permission                                    │
│         (Spatie)                                                 │
│                                                                   │
│   DepartmentRole (User + Department + Role mapping)             │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                      TASK WORKFLOW DOMAIN                        │
│                                                                   │
│   MonthlyPlan ◄── Task                                           │
│    (creator)      (unified task model)                           │
│                    │                                             │
│                    ├──► TaskStateHistory (audit trail)           │
│                    ├──► TaskSLAHistory (SLA tracking)            │
│                    ├──► TaskConfiguration (workflow settings)    │
│                    │                                             │
│                    └──► Operator (many-to-many via pivot)        │
│                                                                   │
│   Task:                                                          │
│   - task_type: enum (mpfm, wireline, production, etc.)          │
│   - state: TaskState enum (DRAFT, SUBMITTED, etc.)              │
│   - department_id: FK → Department                              │
│   - type_specific_data: JSON (flexible per task type)           │
│   - expected_completion_at: SLA deadline                        │
│   - priority: low/normal/high/critical                          │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                   MPFM TESTING DOMAIN                            │
│                                                                   │
│   Task ──(when type='mpfm')──► MPFMTest                         │
│                                 │                                │
│                                 ├──► MPFMTestMetric (50+ typed)  │
│                                 │                                │
│                                 ├──► MPFMValidationRule          │
│                                 │    (configuration)             │
│                                 │                                │
│                                 ├──► MPFMTestValidation          │
│                                 │    (results of validation)     │
│                                 │                                │
│                                 ├──► MPFMTestComparison          │
│                                 │    (vs historical data)        │
│                                 │                                │
│                                 └──► MPFMMetricVersion           │
│                                      (calculation method history)│
│                                                                   │
│   MPFMTest relationships:                                        │
│   - user_id: FK → User (operator)                               │
│   - oil_field_id: FK → OilField                                 │
│   - tower_id: FK → Tower                                        │
│   - oil_string_id: FK → OilString                               │
│   - task_id: FK → Task (links to workflow)                      │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                 LIFT CURVE ANALYSIS DOMAIN                       │
│                                                                   │
│   LiftCurve ◄───► LiftCurvePoint                                │
│    (master)       (individual points on curve)                  │
│     │             - gas_rate                                    │
│     │             - oil_rate                                    │
│     │             - pressure_drawdown                           │
│     │             - data_source (MPFM/Production/Wireline)      │
│     │             - created_date                                │
│     │                                                            │
│     ├──► OilString (which well/string)                          │
│     ├──► LiftCurveMetadata (calc method, versioning)            │
│     │                                                            │
│     └──► PerformanceTrend                                       │
│          - trend_direction (up/down/stable)                     │
│          - percent_change                                       │
│          - probable_cause (equipment/geology/system)            │
│          - recommendation                                       │
│                                                                   │
│   PerformancePrediction ◄── LiftCurve                           │
│   - projected_behavior (7-day, 30-day, 90-day)                 │
│   - confidence_level                                            │
│   - risk_assessment                                             │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                  WELL ASSETS DOMAIN                              │
│                                                                   │
│   OilField ──► Tower ──► OilString                              │
│    (parent)    (child)   (production string)                    │
│                          │                                      │
│                          ├──► WellGeometry                       │
│                          │    - tubing_size                      │
│                          │    - casing_size                      │
│                          │    - restrictions                     │
│                          │                                      │
│                          └──► EquipmentHistory                   │
│                               - equipment_type                   │
│                               - installed_date                   │
│                               - removed_date                     │
│                               - maintenance_log                  │
│                                                                   │
│   Relationships to MPFM:                                         │
│   OilString ──(has many)──► MPFMTest                            │
│   OilString ──(has many)──► LiftCurve                           │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    CROSS-DOMAIN REFERENCES                       │
│                                                                   │
│   Task ──► TaskStateHistory (audit)                             │
│   Task ──► TaskSLAHistory (SLA violations)                      │
│                                                                   │
│   MPFM Test ──► LiftCurvePoint (input source)                   │
│   Production Event ──► LiftCurvePoint (input source)            │
│   Wireline Event ──► LiftCurvePoint (input source)              │
│                                                                   │
│   LiftCurve ──► PerformanceTrend (analysis result)              │
│   LiftCurve ──► PerformancePrediction (forecast)                │
└─────────────────────────────────────────────────────────────────┘
```

---

## PART 3: PROPOSED FOLDER STRUCTURE

### 3.1 Laravel Directory Organization

```
app/
├── Domains/
│   │
│   ├── Task/
│   │   ├── Models/
│   │   │   ├── Task.php (unified task model)
│   │   │   ├── TaskStateHistory.php
│   │   │   ├── TaskSLAHistory.php
│   │   │   └── TaskConfiguration.php
│   │   │
│   │   ├── Services/
│   │   │   ├── TaskStateMachine.php (state transitions)
│   │   │   ├── TaskWorkflowEngine.php (orchestration)
│   │   │   ├── TaskSLAMonitor.php (SLA tracking)
│   │   │   ├── TaskAuthorizationPolicy.php (permissions)
│   │   │   └── TaskValidationService.php (input validation)
│   │   │
│   │   ├── Events/
│   │   │   ├── TaskCreated.php
│   │   │   ├── TaskStateTransitioned.php
│   │   │   ├── TaskCompleted.php
│   │   │   └── TaskSLAViolated.php
│   │   │
│   │   ├── Enums/
│   │   │   ├── TaskState.php
│   │   │   ├── TaskType.php
│   │   │   └── TaskPriority.php
│   │   │
│   │   ├── Repositories/
│   │   │   └── TaskRepository.php
│   │   │
│   │   └── Policies/
│   │       └── TaskPolicy.php
│   │
│   ├── MPFM/
│   │   ├── Models/
│   │   │   ├── MPFMTest.php
│   │   │   ├── MPFMTestMetric.php
│   │   │   ├── MPFMValidationRule.php
│   │   │   ├── MPFMTestValidation.php
│   │   │   ├── MPFMTestComparison.php
│   │   │   ├── MPFMMetricVersion.php
│   │   │   └── MPFMDataQualityAssessment.php
│   │   │
│   │   ├── Services/
│   │   │   ├── MPFMLogProcessor.php (VX log parsing)
│   │   │   ├── MPFMCalculationEngine.php (metric computation)
│   │   │   ├── MPFMValidator.php (rule validation)
│   │   │   ├── MPFMComparison.php (trend analysis)
│   │   │   ├── MPFMDataQualityAssessor.php (quality metrics)
│   │   │   ├── MPFMHistoricalTrendAnalyzer.php (trends)
│   │   │   └── MPFMCacheService.php (performance optimization)
│   │   │
│   │   ├── Calculators/
│   │   │   ├── PVTCalculator.php
│   │   │   ├── FlowRateCalculator.php
│   │   │   ├── RatioCalculator.php
│   │   │   └── CumulativeMetricsCalculator.php
│   │   │
│   │   ├── Enums/
│   │   │   └── MetricType.php
│   │   │
│   │   ├── Events/
│   │   │   ├── MPFMTestCreated.php
│   │   │   ├── MPFMTestValidated.php
│   │   │   └── MPFMTestDataQualityChanged.php
│   │   │
│   │   └── Policies/
│   │       └── MPFMTestPolicy.php
│   │
│   ├── LiftAnalysis/
│   │   ├── Models/
│   │   │   ├── LiftCurve.php
│   │   │   ├── LiftCurvePoint.php
│   │   │   ├── PerformanceTrend.php
│   │   │   ├── PerformancePrediction.php
│   │   │   └── LiftCurveMetadata.php
│   │   │
│   │   ├── Services/
│   │   │   ├── LiftCurveCalculationService.php (core algorithm)
│   │   │   ├── LiftCurveGenerationService.php (orchestrate generation)
│   │   │   ├── PerformanceTrendAnalyzer.php (trend detection)
│   │   │   ├── PerformancePredictionService.php (forecasting)
│   │   │   ├── LiftCurveDataIntegrator.php (multi-source aggregation)
│   │   │   └── LiftCurveValidator.php (validation rules)
│   │   │
│   │   ├── Calculators/
│   │   │   ├── LiftCurvePointCalculator.php
│   │   │   ├── DrawdownCalculator.php
│   │   │   ├── StabilityDetector.php
│   │   │   ├── OutlierFilter.php
│   │   │   └── TrendCalculator.php
│   │   │
│   │   ├── Enums/
│   │   │   ├── TrendDirection.php
│   │   │   └── DataSourceType.php
│   │   │
│   │   ├── Events/
│   │   │   ├── LiftCurveGenerated.php
│   │   │   ├── PerformanceTrendDetected.php
│   │   │   └── AnomalyDetected.php
│   │   │
│   │   └── Policies/
│   │       └── LiftCurvePolicy.php
│   │
│   ├── Organization/
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   ├── Department.php
│   │   │   ├── DepartmentConfiguration.php
│   │   │   └── DepartmentRole.php
│   │   │
│   │   ├── Services/
│   │   │   ├── DepartmentConfigurationService.php
│   │   │   ├── DepartmentWorkflowStrategyFactory.php
│   │   │   ├── PermissionAuthorizationService.php
│   │   │   └── UserAccessValidator.php
│   │   │
│   │   ├── Enums/
│   │   │   └── WorkflowStrategy.php
│   │   │
│   │   └── Policies/
│   │       ├── UserPolicy.php
│   │       └── DepartmentPolicy.php
│   │
│   ├── WellAssets/
│   │   ├── Models/
│   │   │   ├── OilField.php
│   │   │   ├── Tower.php
│   │   │   ├── OilString.php
│   │   │   ├── WellGeometry.php
│   │   │   └── EquipmentHistory.php
│   │   │
│   │   ├── Services/
│   │   │   ├── AssetHierarchyService.php
│   │   │   ├── AssetAvailabilityService.php
│   │   │   └── EquipmentHistoryService.php
│   │   │
│   │   └── Policies/
│   │       └── AssetPolicy.php
│   │
│   └── Shared/
│       ├── Enums/
│       │   ├── DataSource.php
│       │   ├── Unit.php
│       │   └── ApprovalStatus.php
│       │
│       ├── ValueObjects/
│       │   ├── Measurement.php (value + unit)
│       │   ├── Pressure.php
│       │   ├── Temperature.php
│       │   ├── FlowRate.php
│       │   └── DateRange.php
│       │
│       └── Exceptions/
│           ├── InvalidStateTransition.php
│           ├── UnauthorizedAccessException.php
│           ├── DataValidationException.php
│           └── LiftCurveCalculationException.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Dashboard/
│   │   │   ├── DashboardController.php (role-based routing)
│   │   │   ├── OperatorDashboardController.php
│   │   │   ├── SupervisorDashboardController.php
│   │   │   ├── DepartmentDashboardController.php
│   │   │   └── AnalyticsDashboardController.php
│   │   │
│   │   ├── Tasks/
│   │   │   ├── TaskController.php (CRUD)
│   │   │   ├── TaskWorkflowController.php (state transitions)
│   │   │   ├── TaskAssignmentController.php (operator assignment)
│   │   │   └── TaskArchiveController.php (completed tasks)
│   │   │
│   │   ├── Planning/
│   │   │   ├── MonthlyPlanController.php
│   │   │   └── PlanningDashboardController.php
│   │   │
│   │   ├── MPFM/
│   │   │   ├── MPFMTestController.php
│   │   │   ├── MPFMUploadController.php (VX log upload)
│   │   │   ├── MPFMValidationController.php
│   │   │   └── MPFMAnalyticsController.php
│   │   │
│   │   ├── LiftAnalysis/
│   │   │   ├── LiftCurveController.php
│   │   │   ├── PerformanceTrendController.php
│   │   │   └── LiftCurveAnalyticsController.php
│   │   │
│   │   ├── Assets/
│   │   │   ├── OilFieldController.php
│   │   │   ├── TowerController.php
│   │   │   └── OilStringController.php
│   │   │
│   │   ├── Organization/
│   │   │   ├── UserController.php
│   │   │   ├── DepartmentController.php
│   │   │   ├── RoleController.php
│   │   │   └── PermissionController.php
│   │   │
│   │   └── Admin/
│   │       ├── SystemConfigurationController.php
│   │       └── AuditLogController.php
│   │
│   ├── Requests/
│   │   ├── Task/
│   │   │   ├── CreateTaskRequest.php
│   │   │   ├── UpdateTaskRequest.php
│   │   │   └── TransitionTaskRequest.php
│   │   │
│   │   ├── MPFM/
│   │   │   ├── CreateMPFMTestRequest.php
│   │   │   ├── UploadMPFMLogRequest.php
│   │   │   └── ValidateMPFMRuleRequest.php
│   │   │
│   │   └── Planning/
│   │       └── CreateMonthlyPlanRequest.php
│   │
│   └── Resources/
│       ├── TaskResource.php
│       ├── MPFMTestResource.php
│       ├── LiftCurveResource.php
│       └── DepartmentResource.php
│
├── Listeners/
│   ├── Task/
│   │   ├── OnTaskStateTransitioned.php
│   │   └── OnTaskCompleted.php
│   │
│   ├── MPFM/
│   │   ├── OnMPFMTestValidated.php
│   │   └── OnMPFMDataQualityChanged.php
│   │
│   └── LiftAnalysis/
│       └── OnLiftCurveGenerated.php
│
├── Notifications/
│   ├── Task/
│   │   ├── TaskAssignedNotification.php
│   │   ├── TaskApprovedNotification.php
│   │   └── TaskSLAViolatedNotification.php
│   │
│   └── MPFM/
│       └── MPFMTestDataQualityNotification.php
│
├── Policies/
│   ├── Task/
│   │   └── TaskPolicy.php
│   │
│   ├── MPFM/
│   │   └── MPFMTestPolicy.php
│   │
│   └── Organization/
│       └── DepartmentPolicy.php
│
├── Casts/
│   ├── TaskState.php (cast enum)
│   ├── TaskType.php (cast enum)
│   └── MetricType.php (cast enum)
│
└── Providers/
    ├── DomainServiceProvider.php (register all domain services)
    ├── EventServiceProvider.php
    └── AuthServiceProvider.php
```

---

## PART 4: EXAMPLE CODE IMPLEMENTATIONS

### 4.1 Refactored Task Model (Unified)

```php
<?php

namespace App\Domains\Task\Models;

use App\Domains\Task\Enums\TaskState;
use App\Domains\Task\Enums\TaskType;
use App\Domains\Task\Enums\TaskPriority;
use App\Domains\Organization\Models\User;
use App\Domains\Organization\Models\Department;
use App\Domains\WellAssets\Models\OilString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    protected $fillable = [
        'monthly_plan_id',
        'department_id',
        'task_type',          // New: unified task type
        'state',
        'priority',
        'oil_string_id',
        'assigned_to_user_id',
        'created_by_user_id',
        'expected_completion_at',
        'actual_completion_at',
        'type_specific_data',  // New: JSON for task-type-specific fields
        'description',
        'notes',
    ];

    protected $casts = [
        'task_type' => TaskType::class,
        'state' => TaskState::class,
        'priority' => TaskPriority::class,
        'type_specific_data' => 'json',
        'expected_completion_at' => 'datetime',
        'actual_completion_at' => 'datetime',
    ];

    /**
     * RELATIONSHIPS
     */

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id')->nullable();
    }

    public function operators(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'task_operators',
            'task_id',
            'operator_id'
        )->withTimestamps();
    }

    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class)->nullable();
    }

    public function stateHistories(): HasMany
    {
        return $this->hasMany(TaskStateHistory::class);
    }

    public function slaHistory(): HasMany
    {
        return $this->hasMany(TaskSLAHistory::class);
    }

    public function configuration(): HasMany
    {
        return $this->hasMany(TaskConfiguration::class);
    }

    /**
     * STATE MACHINE METHODS
     */

    public function isInState(TaskState $state): bool
    {
        return $this->state === $state;
    }

    public function canTransitionTo(TaskState $newState): bool
    {
        $allowedTransitions = [
            TaskState::DRAFT => [TaskState::SUBMITTED_TO_SURVEILLANCE],
            TaskState::SUBMITTED_TO_SURVEILLANCE => [
                TaskState::ASSIGNED_TO_DEPARTMENT,
                TaskState::DRAFT,
            ],
            TaskState::ASSIGNED_TO_DEPARTMENT => [TaskState::IN_PROGRESS],
            TaskState::IN_PROGRESS => [TaskState::DEPARTMENT_REVIEWED],
            TaskState::DEPARTMENT_REVIEWED => [
                TaskState::SURVEILLANCE_REVIEWED,
                TaskState::IN_PROGRESS,
            ],
            TaskState::SURVEILLANCE_REVIEWED => [
                TaskState::JOB_OFFICER_APPROVED,
                TaskState::DEPARTMENT_REVIEWED,
            ],
            TaskState::JOB_OFFICER_APPROVED => [
                TaskState::COMPLETED,
                TaskState::SURVEILLANCE_REVIEWED,
            ],
            TaskState::COMPLETED => [],
        ];

        return in_array(
            $newState,
            $allowedTransitions[$this->state->value] ?? []
        );
    }

    public function getTypeSpecificData(): array
    {
        return $this->type_specific_data ?? [];
    }

    public function setTypeSpecificData(array $data): void
    {
        $this->type_specific_data = array_merge(
            $this->getTypeSpecificData(),
            $data
        );
    }

    /**
     * SCOPES
     */

    public function scopeByState($query, TaskState $state)
    {
        return $query->where('state', $state);
    }

    public function scopeByType($query, TaskType $type)
    {
        return $query->where('task_type', $type);
    }

    public function scopeForDepartment($query, Department $department)
    {
        return $query->where('department_id', $department->id);
    }

    public function scopeOverdue($query)
    {
        return $query->where('expected_completion_at', '<', now())
            ->whereNotIn('state', [TaskState::COMPLETED]);
    }

    public function scopeByPriority($query, TaskPriority $priority)
    {
        return $query->where('priority', $priority);
    }
}
```

---

### 4.2 Task State Machine Service

```php
<?php

namespace App\Domains\Task\Services;

use App\Domains\Task\Models\Task;
use App\Domains\Task\Models\TaskStateHistory;
use App\Domains\Task\Enums\TaskState;
use App\Domains\Task\Events\TaskStateTransitioned;
use App\Domains\Organization\Models\User;
use Illuminate\Database\Eloquent\Model;

class TaskStateMachine
{
    /**
     * Attempt to transition task to a new state
     *
     * @throws InvalidStateTransition
     */
    public function transition(
        Task $task,
        TaskState $newState,
        User $user,
        ?string $notes = null
    ): TaskStateHistory {
        // 1. AUTHORIZATION CHECK
        if (!$this->canUserTransition($task, $newState, $user)) {
            throw new \App\Domains\Shared\Exceptions\InvalidStateTransition(
                "User {$user->id} cannot transition task from {$task->state->value} to {$newState->value}"
            );
        }

        // 2. BUSINESS VALIDATION
        if (!$task->canTransitionTo($newState)) {
            throw new \App\Domains\Shared\Exceptions\InvalidStateTransition(
                "Cannot transition from {$task->state->value} to {$newState->value}"
            );
        }

        // 3. ATOMIC TRANSACTION
        return \DB::transaction(function () use ($task, $newState, $user, $notes) {
            $oldState = $task->state;

            // Update task state
            $task->state = $newState;
            $task->save();

            // Record in audit trail
            $history = TaskStateHistory::create([
                'task_id' => $task->id,
                'from_state' => $oldState,
                'to_state' => $newState,
                'changed_by_user_id' => $user->id,
                'changed_by_role' => $user->roles()->first()?->name,
                'notes' => $notes,
                'changed_at' => now(),
            ]);

            // Update SLA if needed
            $this->updateSLATracking($task, $newState);

            // Fire event for listeners
            event(new TaskStateTransitioned($task, $oldState, $newState, $user));

            return $history;
        });
    }

    /**
     * Check if user has authorization to transition task
     */
    public function canUserTransition(
        Task $task,
        TaskState $newState,
        User $user
    ): bool {
        // Admin can always transition
        if ($user->hasRole('admin')) {
            return true;
        }

        // Role-based transition rules
        $transitionRules = [
            TaskState::DRAFT => ['job_officer'],
            TaskState::SUBMITTED_TO_SURVEILLANCE => ['surveillance_supervisor', 'job_officer'],
            TaskState::ASSIGNED_TO_DEPARTMENT => ['surveillance_supervisor', 'department_supervisor'],
            TaskState::IN_PROGRESS => ['operator', 'mpfm_operator'],
            TaskState::DEPARTMENT_REVIEWED => ['department_supervisor'],
            TaskState::SURVEILLANCE_REVIEWED => ['surveillance_supervisor'],
            TaskState::JOB_OFFICER_APPROVED => ['job_officer'],
            TaskState::COMPLETED => ['job_officer'],
        ];

        $allowedRoles = $transitionRules[$newState->value] ?? [];

        return $user->hasAnyRole($allowedRoles);
    }

    /**
     * Update SLA tracking on state change
     */
    private function updateSLATracking(Task $task, TaskState $newState): void
    {
        $slaConfig = $this->getSLAConfig($task->department, $newState);

        if ($slaConfig) {
            $task->update([
                'expected_completion_at' => now()->addMinutes($slaConfig['duration_minutes']),
            ]);

            TaskSLAHistory::create([
                'task_id' => $task->id,
                'entered_state' => $newState,
                'expected_completion_at' => $task->expected_completion_at,
                'escalation_level' => 0,
            ]);
        }
    }

    /**
     * Get SLA configuration for a state (from department config)
     */
    private function getSLAConfig($department, TaskState $state): ?array
    {
        $config = $department->configuration()?->type_specific_data ?? [];

        return $config['sla_durations'][$state->value] ?? null;
    }
}
```

---

### 4.3 MPFM Calculation Engine Service (Unified)

```php
<?php

namespace App\Domains\MPFM\Services;

use App\Domains\MPFM\Models\MPFMTest;
use App\Domains\MPFM\Models\MPFMTestMetric;
use App\Domains\MPFM\Calculators\PVTCalculator;
use App\Domains\MPFM\Calculators\FlowRateCalculator;
use App\Domains\MPFM\Calculators\RatioCalculator;
use App\Domains\MPFM\Calculators\CumulativeMetricsCalculator;
use Illuminate\Support\Collection;

class MPFMCalculationEngine
{
    private PVTCalculator $pvtCalculator;
    private FlowRateCalculator $flowRateCalculator;
    private RatioCalculator $ratioCalculator;
    private CumulativeMetricsCalculator $cumulativeCalculator;

    public function __construct(
        PVTCalculator $pvtCalculator,
        FlowRateCalculator $flowRateCalculator,
        RatioCalculator $ratioCalculator,
        CumulativeMetricsCalculator $cumulativeCalculator
    ) {
        $this->pvtCalculator = $pvtCalculator;
        $this->flowRateCalculator = $flowRateCalculator;
        $this->ratioCalculator = $ratioCalculator;
        $this->cumulativeCalculator = $cumulativeCalculator;
    }

    /**
     * Calculate all metrics for an MPFM test
     *
     * Replaces: MPFMCalculationService + MPFMDetailedMetricsExtractorService
     */
    public function calculateAllMetrics(MPFMTest $test): Collection
    {
        $metrics = collect();

        // 1. PVT Properties
        $metrics = $metrics->merge(
            $this->pvtCalculator->calculate($test->raw_data)
        );

        // 2. Flow Rates
        $metrics = $metrics->merge(
            $this->flowRateCalculator->calculate($test->raw_data)
        );

        // 3. Ratios (GOR, WLR, GLR)
        $metrics = $metrics->merge(
            $this->ratioCalculator->calculate($metrics)
        );

        // 4. Cumulative & Statistical
        $metrics = $metrics->merge(
            $this->cumulativeCalculator->calculate($metrics)
        );

        // 5. Data Quality Assessment
        $dataQuality = $this->assessDataQuality($metrics);
        $metrics = $metrics->merge($dataQuality);

        // 6. Persist all metrics as typed records
        $this->persistMetrics($test, $metrics);

        return $metrics;
    }

    /**
     * Assess quality of MPFM test data
     */
    private function assessDataQuality(Collection $metrics): Collection
    {
        $quality = collect();

        // Check for outliers
        $outlierCount = $this->detectOutliers($metrics);
        $quality->put('outlier_count', $outlierCount);

        // Check completeness
        $completeness = $metrics->filter(fn($v) => $v !== null)->count() / $metrics->count();
        $quality->put('data_completeness_percent', $completeness * 100);

        // Overall quality score (0-100)
        $qualityScore = $completeness * 100 - ($outlierCount * 5);
        $quality->put('data_quality_score', max(0, min(100, $qualityScore)));

        // Fitness for analysis
        $quality->put('fit_for_lift_curve_analysis', $qualityScore >= 75);

        return $quality;
    }

    /**
     * Detect outliers using IQR method
     */
    private function detectOutliers(Collection $metrics): int
    {
        $values = $metrics->filter(fn($v) => is_numeric($v))->values();

        if ($values->count() < 4) {
            return 0;
        }

        $sorted = $values->sort()->values();
        $q1 = $sorted[intval($sorted->count() * 0.25)];
        $q3 = $sorted[intval($sorted->count() * 0.75)];
        $iqr = $q3 - $q1;

        $lowerBound = $q1 - (1.5 * $iqr);
        $upperBound = $q3 + (1.5 * $iqr);

        return $values->filter(fn($v) => $v < $lowerBound || $v > $upperBound)->count();
    }

    /**
     * Persist metrics as typed database records
     */
    private function persistMetrics(MPFMTest $test, Collection $metrics): void
    {
        $metricsToInsert = $metrics->map(fn($value, $name) => [
            'mpfm_test_id' => $test->id,
            'metric_name' => $name,
            'metric_value' => $value,
            'metric_type' => $this->getMetricType($name),
            'unit' => $this->getMetricUnit($name),
            'calculation_method_version' => config('mpfm.calculation_version'),
            'created_at' => now(),
        ])->values();

        // Batch insert for performance
        MPFMTestMetric::insert($metricsToInsert->toArray());
    }

    private function getMetricType(string $metricName): string
    {
        // ... lookup from config
    }

    private function getMetricUnit(string $metricName): string
    {
        // ... lookup from config
    }
}
```

---

### 4.4 Lift Curve Calculation Service (Core Algorithm)

```php
<?php

namespace App\Domains\LiftAnalysis\Services;

use App\Domains\LiftAnalysis\Models\LiftCurve;
use App\Domains\LiftAnalysis\Models\LiftCurvePoint;
use App\Domains\LiftAnalysis\Calculators\DrawdownCalculator;
use App\Domains\LiftAnalysis\Calculators\StabilityDetector;
use App\Domains\LiftAnalysis\Calculators\OutlierFilter;
use App\Domains\WellAssets\Models\OilString;
use App\Domains\MPFM\Models\MPFMTest;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LiftCurveCalculationService
{
    private DrawdownCalculator $drawdownCalculator;
    private StabilityDetector $stabilityDetector;
    private OutlierFilter $outlierFilter;

    public function __construct(
        DrawdownCalculator $drawdownCalculator,
        StabilityDetector $stabilityDetector,
        OutlierFilter $outlierFilter
    ) {
        $this->drawdownCalculator = $drawdownCalculator;
        $this->stabilityDetector = $stabilityDetector;
        $this->outlierFilter = $outlierFilter;
    }

    /**
     * Generate lift curve for an oil string based on multi-source data
     *
     * ALGORITHM:
     * 1. Collect multi-source data (MPFM, production, wireline)
     * 2. Normalize to common reference conditions
     * 3. Filter for steady-state data
     * 4. Remove outliers
     * 5. Calculate pressure drawdown
     * 6. Plot Qo vs ΔP
     * 7. Fit curve (polynomial or exponential)
     * 8. Assess trend vs baseline
     */
    public function generateLiftCurve(
        OilString $oilString,
        Carbon $startDate,
        Carbon $endDate,
        ?LiftCurve $baseline = null
    ): LiftCurve {
        // STEP 1: Collect multi-source data
        $data = $this->aggregateData($oilString, $startDate, $endDate);

        // STEP 2: Normalize to reference conditions
        $normalizedData = $this->normalizeData($data);

        // STEP 3: Filter for steady-state (remove equipment-off, testing, etc.)
        $steadyStateData = $this->filterSteadyState($normalizedData);

        // STEP 4: Remove outliers
        $cleanData = $this->outlierFilter->filter($steadyStateData);

        if ($cleanData->count() < 5) {
            throw new \Exception('Insufficient data points for lift curve generation');
        }

        // STEP 5: Calculate pressure drawdown for each point
        $curvePoints = $cleanData->map(function ($point) {
            return $this->drawdownCalculator->calculate($point);
        })->values();

        // STEP 6: Fit curve (polynomial or exponential)
        $fittedCurve = $this->fitCurve($curvePoints);

        // STEP 7: Create or update lift curve record
        $liftCurve = $this->saveLiftCurve(
            $oilString,
            $curvePoints,
            $fittedCurve,
            $startDate,
            $endDate
        );

        // STEP 8: Assess trend vs baseline
        if ($baseline) {
            $this->assessTrend($liftCurve, $baseline);
        }

        return $liftCurve;
    }

    /**
     * Aggregate multi-source data
     *
     * Sources:
     * 1. MPFM Tests - oil/gas/water rates, pressures, temperatures
     * 2. Production Events - daily rates, choke positions
     * 3. Wireline Events - downhole pressures, fluid levels
     * 4. Completion Data - equipment specifications
     * 5. Maintenance Events - workover dates, equipment changes
     */
    private function aggregateData(
        OilString $oilString,
        Carbon $startDate,
        Carbon $endDate
    ): Collection {
        $data = collect();

        // 1. MPFM Tests
        $mpfmTests = MPFMTest::where('oil_string_id', $oilString->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($mpfmTests as $test) {
            $data->push([
                'source' => 'mpfm',
                'source_id' => $test->id,
                'timestamp' => $test->created_at,
                'oil_rate' => $test->oil_rate,
                'gas_rate' => $test->gas_rate,
                'water_cut' => $test->water_cut,
                'whp' => $test->pressure,  // Well head pressure
                'wht' => $test->temperature,
                'data_quality_score' => $this->getDataQuality($test),
            ]);
        }

        // 2. Production Operations Data
        // (Assuming DailyProduction model exists)
        $dailyProduction = \App\Models\DailyProduction::where('oil_string_id', $oilString->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        foreach ($dailyProduction as $prod) {
            $data->push([
                'source' => 'production',
                'source_id' => $prod->id,
                'timestamp' => $prod->date,
                'oil_rate' => $prod->daily_oil_rate,
                'choke_position' => $prod->choke_position,
                'whp' => $prod->whp,
                'data_quality_score' => 80,  // Assuming high quality
            ]);
        }

        // 3. Wireline Data
        // (Assuming WirelineEvent model exists)
        $wireline = \App\Models\WirelineEvent::where('oil_string_id', $oilString->id)
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get();

        foreach ($wireline as $event) {
            $data->push([
                'source' => 'wireline',
                'source_id' => $event->id,
                'timestamp' => $event->event_date,
                'downhole_pressure' => $event->bhp,
                'fluid_level' => $event->fluid_level,
                'data_quality_score' => 90,
            ]);
        }

        return $data->sortBy('timestamp');
    }

    /**
     * Normalize data to reference conditions
     * (Gas/oil at standard temperature and pressure)
     */
    private function normalizeData(Collection $data): Collection
    {
        return $data->map(function ($point) {
            // Normalize temperature
            $normalizedOilRate = $this->normalizeFlowRate(
                $point['oil_rate'] ?? 0,
                $point['wht'] ?? 20  // 20°C reference
            );

            // Normalize pressure if available
            $normalizedGasRate = $this->normalizeFlowRate(
                $point['gas_rate'] ?? 0,
                $point['whp'] ?? 101.325  // 101.325 kPa reference
            );

            return array_merge($point, [
                'oil_rate_normalized' => $normalizedOilRate,
                'gas_rate_normalized' => $normalizedGasRate,
            ]);
        });
    }

    /**
     * Filter for steady-state data
     * (Remove start/stop periods, equipment changes, etc.)
     */
    private function filterSteadyState(Collection $data): Collection
    {
        return $data->filter(function ($point) {
            // Check if data represents steady-state operation
            return $this->stabilityDetector->isStable($point);
        })->values();
    }

    /**
     * Fit polynomial or exponential curve to data points
     */
    private function fitCurve(Collection $curvePoints): array
    {
        // Use least-squares regression to fit a curve
        // Supports: linear, polynomial (2-3 degree), exponential

        $qoValues = $curvePoints->pluck('oil_rate_normalized')->toArray();
        $dpValues = $curvePoints->pluck('pressure_drawdown')->toArray();

        // Try polynomial fit first (most common for IPR curves)
        $coefficients = $this->polynomialRegression($dpValues, $qoValues, 2);

        return [
            'curve_type' => 'polynomial_2',
            'coefficients' => $coefficients,
            'r_squared' => $this->calculateRSquared($dpValues, $qoValues, $coefficients),
        ];
    }

    /**
     * Polynomial regression using least squares
     */
    private function polynomialRegression(array $x, array $y, int $degree): array
    {
        // Matrix math for polynomial fit
        // Returns coefficients [a0, a1, a2, ...] for y = a0 + a1*x + a2*x^2 + ...

        // Simplified implementation - use proper math library in production
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = array_sum(array_map(fn($a, $b) => $a * $b, $x, $y));
        $sumX2 = array_sum(array_map(fn($a) => $a ** 2, $x));
        $sumX2Y = array_sum(array_map(fn($a, $b) => ($a ** 2) * $b, $x, $y));

        $denom = ($n * $sumX2) - ($sumX ** 2);

        return [
            'a0' => (($sumY * $sumX2) - ($sumX * $sumX2Y)) / $denom,
            'a1' => (($n * $sumX2Y) - ($sumX * $sumY)) / $denom,
        ];
    }

    /**
     * Save lift curve to database
     */
    private function saveLiftCurve(
        OilString $oilString,
        Collection $points,
        array $fittedCurve,
        Carbon $startDate,
        Carbon $endDate
    ): LiftCurve {
        $liftCurve = LiftCurve::firstOrNew([
            'oil_string_id' => $oilString->id,
        ]);

        $liftCurve->update([
            'reference_date' => now(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'curve_type' => $fittedCurve['curve_type'],
            'curve_coefficients' => $fittedCurve['coefficients'],
            'r_squared' => $fittedCurve['r_squared'],
            'data_points_count' => $points->count(),
            'calculation_status' => 'completed',
        ]);

        // Save individual curve points
        foreach ($points as $point) {
            LiftCurvePoint::create([
                'lift_curve_id' => $liftCurve->id,
                'oil_rate' => $point['oil_rate_normalized'],
                'gas_rate' => $point['gas_rate_normalized'] ?? null,
                'pressure_drawdown' => $point['pressure_drawdown'],
                'temperature' => $point['wht'] ?? null,
                'data_source' => $point['source'],
                'source_id' => $point['source_id'],
                'created_at' => $point['timestamp'],
            ]);
        }

        return $liftCurve->refresh();
    }

    /**
     * Assess trend vs baseline curve
     */
    private function assessTrend(LiftCurve $current, LiftCurve $baseline): void
    {
        // Compare current curve to baseline
        // Detect: improvement, degradation, stable

        $currentCoeff = $current->curve_coefficients;
        $baselineCoeff = $baseline->curve_coefficients;

        $percentChange = (($currentCoeff['a0'] - $baselineCoeff['a0']) / $baselineCoeff['a0']) * 100;

        $trend = $percentChange > 5 ? 'improving' :
                 ($percentChange < -5 ? 'degrading' : 'stable');

        // Save trend analysis
        \App\Domains\LiftAnalysis\Models\PerformanceTrend::create([
            'lift_curve_id' => $current->id,
            'baseline_lift_curve_id' => $baseline->id,
            'trend_direction' => $trend,
            'percent_change' => $percentChange,
            'probable_cause' => $this->determineCause($current, $baseline),
            'recommendation' => $this->getRecommendation($trend),
        ]);
    }

    private function determineCause(LiftCurve $current, LiftCurve $baseline): string
    {
        // Analyze what caused the trend
        // Equipment degradation, formation damage, scale, etc.
        return 'Formation damage or equipment degradation';
    }

    private function getRecommendation(string $trend): string
    {
        return match ($trend) {
            'improving' => 'Continue current operations',
            'degrading' => 'Schedule maintenance or chemical treatment',
            'stable' => 'Monitor performance',
            default => 'Further analysis required',
        };
    }

    private function normalizeFlowRate($rate, $reference): float
    {
        // Normalize flow rate to reference conditions
        return $rate * ($reference / 20);  // Simplified
    }

    private function getDataQuality($test): float
    {
        // Get data quality score from MPFM test
        return $test->getTypeSpecificData()['data_quality_score'] ?? 0;
    }

    private function calculateRSquared($x, $y, $coefficients): float
    {
        // Calculate R² goodness-of-fit
        $yMean = array_sum($y) / count($y);
        $ssTotal = array_sum(array_map(fn($val) => ($val - $yMean) ** 2, $y));

        $predicted = array_map(
            fn($xi) => $coefficients['a0'] + ($coefficients['a1'] * $xi),
            $x
        );

        $ssRes = array_sum(array_map(
            fn($yi, $pi) => ($yi - $pi) ** 2,
            $y,
            $predicted
        ));

        return 1 - ($ssRes / $ssTotal);
    }
}
```

---

### 4.5 Task Workflow Engine Service

```php
<?php

namespace App\Domains\Task\Services;

use App\Domains\Task\Models\Task;
use App\Domains\Task\Enums\TaskState;
use App\Domains\Task\Enums\TaskType;
use App\Domains\Organization\Models\User;
use App\Domains\Organization\Services\PermissionAuthorizationService;

class TaskWorkflowEngine
{
    private TaskStateMachine $stateMachine;
    private PermissionAuthorizationService $authService;
    private TaskSLAMonitor $slaMonitor;

    public function __construct(
        TaskStateMachine $stateMachine,
        PermissionAuthorizationService $authService,
        TaskSLAMonitor $slaMonitor
    ) {
        $this->stateMachine = $stateMachine;
        $this->authService = $authService;
        $this->slaMonitor = $slaMonitor;
    }

    /**
     * Create a new task (Job Officer workflow)
     */
    public function createTask(
        TaskType $type,
        array $typeSpecificData,
        User $creator,
        $department,
        $expectedCompletion = null
    ): Task {
        return \DB::transaction(function () use (
            $type,
            $typeSpecificData,
            $creator,
            $department,
            $expectedCompletion
        ) {
            $task = Task::create([
                'task_type' => $type,
                'type_specific_data' => $typeSpecificData,
                'created_by_user_id' => $creator->id,
                'department_id' => $department->id,
                'state' => TaskState::DRAFT,
                'expected_completion_at' => $expectedCompletion,
            ]);

            // Record initial state
            \App\Domains\Task\Models\TaskStateHistory::create([
                'task_id' => $task->id,
                'from_state' => null,
                'to_state' => TaskState::DRAFT,
                'changed_by_user_id' => $creator->id,
                'changed_by_role' => $creator->roles()->first()?->name,
                'changed_at' => now(),
            ]);

            return $task;
        });
    }

    /**
     * Submit task to surveillance (Job Officer → Surveillance flow)
     */
    public function submitToSurveillance(Task $task, User $submitter, ?string $notes = null): void
    {
        $this->stateMachine->transition(
            $task,
            TaskState::SUBMITTED_TO_SURVEILLANCE,
            $submitter,
            $notes
        );
    }

    /**
     * Assign task to department (Surveillance → Department flow)
     */
    public function assignToDepartment(
        Task $task,
        User $supervisor,
        User $assignedOperator,
        ?string $notes = null
    ): void {
        $this->stateMachine->transition(
            $task,
            TaskState::ASSIGNED_TO_DEPARTMENT,
            $supervisor,
            $notes
        );

        // Assign operator
        $task->operators()->sync([$assignedOperator->id]);
    }

    /**
     * Start task execution (Operator flow)
     */
    public function startTask(Task $task, User $operator): void
    {
        if (!$task->operators()->where('user_id', $operator->id)->exists()) {
            throw new \Exception("Operator not assigned to this task");
        }

        $this->stateMachine->transition(
            $task,
            TaskState::IN_PROGRESS,
            $operator
        );
    }

    /**
     * Complete operator work and submit for review
     */
    public function submitForReview(
        Task $task,
        User $operator,
        array $resultData
    ): void {
        $task->setTypeSpecificData([
            'operator_result_data' => $resultData,
            'submitted_at' => now(),
        ]);
        $task->save();

        $this->stateMachine->transition(
            $task,
            TaskState::DEPARTMENT_REVIEWED,
            $operator,
            "Submitted for supervisor review"
        );
    }

    /**
     * Department supervisor reviews and approves
     */
    public function departmentApprove(
        Task $task,
        User $supervisor,
        ?string $notes = null
    ): void {
        $this->stateMachine->transition(
            $task,
            TaskState::SURVEILLANCE_REVIEWED,
            $supervisor,
            $notes
        );
    }

    /**
     * Department supervisor rejects and sends back
     */
    public function departmentReject(
        Task $task,
        User $supervisor,
        string $rejectionReason
    ): void {
        $this->stateMachine->transition(
            $task,
            TaskState::IN_PROGRESS,  // Back to in-progress for rework
            $supervisor,
            "Rejected: {$rejectionReason}"
        );
    }

    /**
     * Surveillance supervisor final review
     */
    public function surveillanceApprove(
        Task $task,
        User $surveillanceSupervisor,
        ?string $notes = null
    ): void {
        $this->stateMachine->transition(
            $task,
            TaskState::JOB_OFFICER_APPROVED,
            $surveillanceSupervisor,
            $notes
        );
    }

    /**
     * Job Officer final approval and completion
     */
    public function jobOfficerApprove(
        Task $task,
        User $jobOfficer,
        ?string $notes = null
    ): void {
        $this->stateMachine->transition(
            $task,
            TaskState::COMPLETED,
            $jobOfficer,
            $notes ?? "Approved and completed"
        );

        $task->update([
            'actual_completion_at' => now(),
        ]);
    }

    /**
     * Get all accessible tasks for a user
     */
    public function getAccessibleTasks(User $user): \Illuminate\Database\Eloquent\Collection
    {
        $query = Task::query();

        if ($user->hasRole('admin')) {
            return $query->get();
        }

        if ($user->hasRole('surveillance_supervisor')) {
            // Can see all tasks
            return $query->get();
        }

        if ($user->hasRole('department_supervisor')) {
            // Can see only their department's tasks
            return $query->where('department_id', $user->department_id)->get();
        }

        if ($user->hasRole('operator')) {
            // Can see only assigned tasks
            return $query->whereHas('operators', fn($q) => $q->where('user_id', $user->id))->get();
        }

        if ($user->hasRole('job_officer')) {
            // Can see all tasks (planning authority)
            return $query->get();
        }

        return $query->where('created_by_user_id', $user->id)->get();
    }

    /**
     * Monitor SLA compliance
     */
    public function checkAndEscalateSLAs(): void
    {
        $overdueTasks = Task::where('expected_completion_at', '<', now())
            ->whereNotIn('state', [TaskState::COMPLETED])
            ->get();

        foreach ($overdueTasks as $task) {
            $this->slaMonitor->escalate($task);
        }
    }
}
```

---

## PART 5: LIFT CURVE ALGORITHM DETAILED DESIGN

### 5.1 Input Requirements

```
MINIMUM DATA FOR LIFT CURVE GENERATION:
├── Oil Rate (BOPD) - Source: MPFM or Production
├── Gas Rate (MMSCFD) - Source: MPFM
├── Water Cut (%) - Source: MPFM
├── Well Head Pressure (kPa) - Source: MPFM or Production
├── Temperature (°C) - Source: MPFM
├── Tubing Size (inches) - Source: Completion Data
├── Number of Data Points: Minimum 5, Recommended 15-30
└── Data Span: Minimum 7 days, Recommended 30+ days

TIME SERIES REQUIREMENTS:
├── Data must span steady-state operations
├── Equipment OFF periods must be excluded
├── Choke/restriction changes must be documented
├── Maintenance events must be marked
└── Data quality score must be ≥ 60%

DATA QUALITY INDICATORS:
├── Completeness: % of non-null values
├─�� Outlier Count: Data points beyond ±3σ
├── Measurement Precision: ±1% for flow, ±2% for pressure
├── Stability Index: Variance within acceptable range
└── Source Reliability: MPFM > Production > Wireline (priority)
```

### 5.2 Filtering Logic

```
STEP 1: STABILITY DETECTION
├── Calculate moving average (7-point window)
├── Detect regime changes (>10% rate change)
├── Flag equipment start/stop periods
├── Exclude first 6 hours after startup
├── Exclude last 6 hours before shutdown
└── Keep only continuous stable periods (>4 hours)

STEP 2: OUTLIER REMOVAL
├── Method: Interquartile Range (IQR)
├── Calculate Q1, Q3, IQR
├── Remove if: X < Q1 - 1.5×IQR OR X > Q3 + 1.5×IQR
├── Alternative: Z-score > 3σ
└── Log removed points for quality assessment

STEP 3: DATA NORMALIZATION
├── Temperature: 0°C (standard condition)
├── Pressure: 101.325 kPa (standard condition)
├── Flow Rate: Normalize using gas law
├── Water Cut: Convert to mass basis if needed
└── Match units: BOPD, MMSCFD, kPa, °C

STEP 4: DUPLICATION CHECK
├── Remove duplicate timestamps (keep first)
├── Remove identical measurements (instrumental error)
├── Remove consecutive identical values >4 points
└── Log deduplicated records
```

### 5.3 Pressure Drawdown Calculation

```
PRESSURE DRAWDOWN (ΔP) = AVERAGE RESERVOIR PRESSURE - FLOWING WELL PRESSURE

Estimation Method:
├── If downhole pressure available: Use directly
├── Else if tubing size known:
│   ├── Calculate hydrostatic column (fluid + gas)
│   ├── Account for friction losses
│   ├── ΔP_bottom = P_whp + Hydrostatic - Friction
│   └── ΔP = P_res - ΔP_bottom
└── Else: Use empirical correlation (e.g., Poettmann-Carpenter)

For Each Data Point:
  WhP = Well head pressure (measured)
  Qo = Oil rate
  Qg = Gas rate
  WC = Water cut
  T = Temperature

  → Calculate density (ρ) of flowing fluid
  → Calculate hydrostatic pressure (h × ρ × g)
  → Calculate friction losses (f × L × v² / (2 × d))
  → ΔP = P_flowing_bottom - P_static_bottom
```

### 5.4 Curve Fitting Algorithm

```
POLYNOMIAL FIT (IPR Equation: Qo = a + b×ΔP + c×ΔP²)

Method: Least Squares Regression

Given: n data points (ΔPᵢ, Qoᵢ)

Minimize: Σ(Qoᵢ - (a + b×ΔPᵢ + c×ΔPᵢ²))²

Matrix Form:
  [n        ΣΔP      ΣΔP²   ] [a]   [ΣQo    ]
  [ΣΔP      ΣΔP²     ΣΔP³   ] [b] = [ΣQo×ΔP]
  [ΣΔP²     ΣΔP³     ΣΔP⁴   ] [c]   [ΣQo×ΔP²]

Solve using Gaussian elimination or Cramer's rule

VALIDATE FIT:
├── Calculate R² (coefficient of determination)
├── R² > 0.95 = Excellent fit
├── R² > 0.85 = Good fit
├── R² > 0.70 = Acceptable fit
├── R² < 0.70 = Recheck data quality
└── If poor fit: Try exponential model

EXPONENTIAL ALTERNATIVE:
├── Qo = a × e^(b×ΔP)
├── Transform: ln(Qo) = ln(a) + b×ΔP
├── Use linear regression on log transform
└── Back-transform to get original coefficients
```

### 5.5 Performance Trend Analysis

```
BASELINE CURVE: Previous stable period (typically 7-30 days old)
CURRENT CURVE: Latest measurements

COMPARISON METRICS:

1. Curve Shift:
   ├── At ΔP=100: Compare Qo_current vs Qo_baseline
   ├── Percent change = (Qo_current - Qo_baseline) / Qo_baseline × 100%
   ├── If > +5%: IMPROVING
   ├── If < -5%: DEGRADING
   └── Else: STABLE

2. Curve Steepness (Rate of change):
   ├── dQo/dΔP = slope of IPR
   ├── Decreased slope = Higher flow resistance
   ├── Likely causes: Scale, sand, formation damage
   └── Increased slope = Better injectivity

3. Absolute Productivity Index:
   ├── J = Qo / ΔP (at operating point)
   ├── Compare J_current to J_baseline
   ├── Indicates well's drainage efficiency
   └── Changes signal geological/equipment issues

4. Damage Ratio:
   ├── S = (J_ideal - J_actual) / J_actual
   ├── S > 0: Skin damage (formation damage, scale)
   ├── S < 0: Stimulation effect (well cleanup)
   └── |S| > 2: Significant intervention needed

ANOMALY DETECTION:
├── Sudden rate drop (>15%) → Equipment failure
├── Sudden pressure rise (>20%) → Blockage
├── Curve inversion → Data quality issue
├── High variance → Unstable operation
└── Zero production → Well shut-in
```

### 5.6 Confidence & Risk Assessment

```
DATA QUALITY SCORE (0-100):
├── Completeness (30%): % of expected measurements
├── Accuracy (30%): Instrument calibration + drift
├── Outliers (20%): % of points flagged
├── Stability (20%): Variance in repeated measurements
└── Total = Weighted average

CONFIDENCE LEVEL:
├── Excellent (90-100%): Quality >85%, R² >0.95, n>20 points
├── Good (70-89%): Quality 70-85%, R² 0.85-0.95, n 15-20 points
├── Fair (50-69%): Quality 50-70%, R² 0.70-0.85, n 10-15 points
├── Poor (<50%): Quality <50%, R² <0.70, n <10 points
└── Recommendation: Only use Fair+ for production decisions

PREDICTION CONFIDENCE:
├── 7-day forecast: ±10% (if data available 30+ days)
├── 30-day forecast: ±20%
├── 90-day forecast: ±35%
└── Beyond 90 days: Not recommended

RISK FACTORS:
├── Scale formation risk: If trend shows degradation
├── Sand production risk: If high sand cut observed
├── Pump cavitation risk: If high gas rate (GOR >500)
├── Equipment failure risk: If rapid rate drop
└── Formation damage risk: If skin trend positive
```

---

## PART 6: DATABASE IMPROVEMENTS & MIGRATIONS

### 6.1 New Tables & Structures

```sql
-- TASK WORKFLOW DOMAIN
CREATE TABLE tasks (
    id BIGINT PRIMARY KEY,
    monthly_plan_id BIGINT,
    department_id BIGINT NOT NULL,
    task_type VARCHAR(50) NOT NULL,  -- ENUM: mpfm, wireline, production, drilling, completion, workover
    state VARCHAR(50) NOT NULL,      -- ENUM: draft, submitted, assigned, in_progress, reviewed, approved, completed
    priority VARCHAR(20),             -- ENUM: low, normal, high, critical
    oil_string_id BIGINT,
    assigned_to_user_id BIGINT,
    created_by_user_id BIGINT NOT NULL,
    expected_completion_at TIMESTAMP,
    actual_completion_at TIMESTAMP,
    type_specific_data JSON,  -- Store task-type-specific data
    description TEXT,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP,

    FOREIGN KEY (monthly_plan_id) REFERENCES monthly_plans(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (oil_string_id) REFERENCES oil_strings(id),
    FOREIGN KEY (assigned_to_user_id) REFERENCES users(id),
    FOREIGN KEY (created_by_user_id) REFERENCES users(id),

    -- INDEXES
    INDEX idx_state (state),
    INDEX idx_department (department_id),
    INDEX idx_task_type (task_type),
    INDEX idx_expected_completion (expected_completion_at),
    INDEX idx_assigned_to (assigned_to_user_id),
    INDEX idx_oil_string (oil_string_id),
    INDEX idx_created_by (created_by_user_id),
    INDEX idx_state_department (state, department_id),
    INDEX idx_state_assigned (state, assigned_to_user_id)
);

CREATE TABLE task_operators (
    id BIGINT PRIMARY KEY,
    task_id BIGINT NOT NULL,
    operator_id BIGINT NOT NULL,
    assigned_at TIMESTAMP,
    assigned_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (operator_id) REFERENCES users(id),
    FOREIGN KEY (assigned_by) REFERENCES users(id),

    UNIQUE KEY unique_task_operator (task_id, operator_id),
    INDEX idx_operator (operator_id)
);

CREATE TABLE task_state_histories (
    id BIGINT PRIMARY KEY,
    task_id BIGINT NOT NULL,
    from_state VARCHAR(50),
    to_state VARCHAR(50) NOT NULL,
    changed_by_user_id BIGINT NOT NULL,
    changed_by_role VARCHAR(100),
    notes TEXT,
    changed_at TIMESTAMP,
    created_at TIMESTAMP,

    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by_user_id) REFERENCES users(id),

    INDEX idx_task_state (task_id, to_state),
    INDEX idx_changed_at (changed_at)
);

CREATE TABLE task_sla_histories (
    id BIGINT PRIMARY KEY,
    task_id BIGINT NOT NULL,
    entered_state VARCHAR(50),
    expected_completion_at TIMESTAMP,
    actual_completion_at TIMESTAMP,
    escalation_level INT DEFAULT 0,
    escalated_at TIMESTAMP,
    escalated_to BIGINT,
    resolved_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (escalated_to) REFERENCES users(id),

    INDEX idx_task_sla (task_id),
    INDEX idx_escalation_level (escalation_level),
    INDEX idx_expected_completion (expected_completion_at)
);

CREATE TABLE task_configurations (
    id BIGINT PRIMARY KEY,
    task_id BIGINT NOT NULL,
    config_key VARCHAR(255),
    config_value JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,

    UNIQUE KEY unique_task_config (task_id, config_key)
);

-- MPFM DOMAIN
CREATE TABLE mpfm_test_metrics (
    id BIGINT PRIMARY KEY,
    mpfm_test_id BIGINT NOT NULL,
    metric_name VARCHAR(100) NOT NULL,
    metric_value DECIMAL(15,6),
    metric_type VARCHAR(50),  -- ENUM: flow_rate, pressure, ratio, temperature, etc.
    unit VARCHAR(50),
    calculation_method_version VARCHAR(20),
    is_derived BOOLEAN DEFAULT false,
    created_at TIMESTAMP,

    FOREIGN KEY (mpfm_test_id) REFERENCES mpfm_tests(id) ON DELETE CASCADE,

    INDEX idx_test_metric (mpfm_test_id, metric_name),
    INDEX idx_metric_name (metric_name),
    INDEX idx_metric_type (metric_type)
);

CREATE TABLE mpfm_metric_versions (
    id BIGINT PRIMARY KEY,
    metric_name VARCHAR(100),
    version VARCHAR(20),
    calculation_method TEXT,
    change_description TEXT,
    effective_from TIMESTAMP,
    created_at TIMESTAMP,

    UNIQUE KEY unique_metric_version (metric_name, version)
);

-- LIFT CURVE DOMAIN
CREATE TABLE lift_curves (
    id BIGINT PRIMARY KEY,
    oil_string_id BIGINT NOT NULL,
    reference_date TIMESTAMP,
    start_date DATE,
    end_date DATE,
    curve_type VARCHAR(50),  -- polynomial_2, polynomial_3, exponential, etc.
    curve_coefficients JSON,  -- {a0, a1, a2, ...}
    r_squared DECIMAL(5,4),
    data_points_count INT,
    calculation_status VARCHAR(50),  -- pending, completed, failed
    calculation_error TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP,

    FOREIGN KEY (oil_string_id) REFERENCES oil_strings(id),

    INDEX idx_oil_string (oil_string_id),
    INDEX idx_reference_date (reference_date),
    INDEX idx_status (calculation_status),
    UNIQUE KEY unique_oil_string_date (oil_string_id, reference_date)
);

CREATE TABLE lift_curve_points (
    id BIGINT PRIMARY KEY,
    lift_curve_id BIGINT NOT NULL,
    oil_rate DECIMAL(10,2),
    gas_rate DECIMAL(10,2),
    pressure_drawdown DECIMAL(10,2),
    temperature DECIMAL(5,2),
    data_source VARCHAR(50),  -- ENUM: mpfm, production, wireline
    source_id BIGINT,
    created_at TIMESTAMP,

    FOREIGN KEY (lift_curve_id) REFERENCES lift_curves(id) ON DELETE CASCADE,

    INDEX idx_lift_curve (lift_curve_id),
    INDEX idx_source (data_source)
);

CREATE TABLE performance_trends (
    id BIGINT PRIMARY KEY,
    lift_curve_id BIGINT NOT NULL,
    baseline_lift_curve_id BIGINT,
    trend_direction VARCHAR(50),  -- ENUM: improving, degrading, stable
    percent_change DECIMAL(6,2),
    probable_cause VARCHAR(255),
    recommendation TEXT,
    created_at TIMESTAMP,

    FOREIGN KEY (lift_curve_id) REFERENCES lift_curves(id) ON DELETE CASCADE,
    FOREIGN KEY (baseline_lift_curve_id) REFERENCES lift_curves(id),

    INDEX idx_lift_curve (lift_curve_id),
    INDEX idx_trend (trend_direction)
);

CREATE TABLE performance_predictions (
    id BIGINT PRIMARY KEY,
    lift_curve_id BIGINT NOT NULL,
    forecast_date DATE,
    forecast_window VARCHAR(50),  -- 7_day, 30_day, 90_day
    projected_behavior JSON,  -- {qo_7day, qo_30day, ...}
    confidence_level DECIMAL(3,0),
    risk_assessment JSON,
    created_at TIMESTAMP,

    FOREIGN KEY (lift_curve_id) REFERENCES lift_curves(id) ON DELETE CASCADE,

    INDEX idx_lift_curve (lift_curve_id),
    INDEX idx_forecast_date (forecast_date)
);

CREATE TABLE lift_curve_metadata (
    id BIGINT PRIMARY KEY,
    lift_curve_id BIGINT NOT NULL,
    calculation_method VARCHAR(100),
    method_version VARCHAR(20),
    data_sources JSON,
    filtering_rules JSON,
    stability_parameters JSON,
    outlier_count INT,
    quality_score DECIMAL(5,2),
    created_at TIMESTAMP,

    FOREIGN KEY (lift_curve_id) REFERENCES lift_curves(id) ON DELETE CASCADE
);

-- ORGANIZATION DOMAIN
CREATE TABLE department_configurations (
    id BIGINT PRIMARY KEY,
    department_id BIGINT NOT NULL UNIQUE,
    workflow_strategy VARCHAR(100),
    sla_durations JSON,
    approval_hierarchy JSON,
    kpi_definitions JSON,
    data_retention_days INT DEFAULT 2555,
    requires_validation BOOLEAN DEFAULT true,
    type_specific_data JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
);

CREATE TABLE department_roles (
    id BIGINT PRIMARY KEY,
    department_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    role_id BIGINT NOT NULL,
    assigned_at TIMESTAMP,
    assigned_by BIGINT,
    created_at TIMESTAMP,

    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (assigned_by) REFERENCES users(id),

    UNIQUE KEY unique_dept_user_role (department_id, user_id, role_id)
);

-- WELL ASSETS DOMAIN
CREATE TABLE well_geometries (
    id BIGINT PRIMARY KEY,
    oil_string_id BIGINT NOT NULL,
    tubing_size_inches DECIMAL(5,3),
    casing_size_inches DECIMAL(5,3),
    depth_md INT,  -- Measured depth
    depth_tvd INT,  -- True vertical depth
    restrictions JSON,  -- {type, location, severity}
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (oil_string_id) REFERENCES oil_strings(id),
    UNIQUE KEY unique_oil_string_geometry (oil_string_id)
);

CREATE TABLE equipment_histories (
    id BIGINT PRIMARY KEY,
    oil_string_id BIGINT NOT NULL,
    equipment_type VARCHAR(100),  -- pump, separator, valve, etc.
    installed_date TIMESTAMP,
    removed_date TIMESTAMP,
    maintenance_log JSON,
    specifications JSON,
    created_at TIMESTAMP,

    FOREIGN KEY (oil_string_id) REFERENCES oil_strings(id),

    INDEX idx_oil_string (oil_string_id),
    INDEX idx_installed_date (installed_date)
);
```

### 6.2 Migration Strategy (Phased)

```
PHASE 1: BACKUP & PREPARE (Week 1)
├── Full database backup
├── Create migration testing environment
├── Document current data structure
└── Notify stakeholders

PHASE 2: ADD NEW TABLES (Week 2)
├── Create all new domain tables (non-breaking)
├── Add indexes for performance
├── Create triggers for automatic fields
└── Run in staging environment

PHASE 3: DATA MIGRATION (Week 3)
├── Migrate PlanTask → Task (preserve state, add task_type)
├── Create task_state_histories from existing audit logs
├── Create task_sla_histories from existing SLA data
├── Migrate MPFM JSON metrics → MPFMTestMetric records
└── Validate data integrity (row counts, sums, etc.)

PHASE 4: DEPLOY NEW CODE (Week 4)
├── Deploy new domain services
├── Deploy new controllers (shadow mode alongside old)
├── Deploy new models
├── Enable feature flags

PHASE 5: CUTOVER (Week 5)
├── Run parallel processing (old + new systems)
├── Compare results
├── Monitor performance
├── Switch traffic to new system
└── Keep old system read-only for 2 weeks

PHASE 6: CLEANUP (Week 6+)
├── Archive old tables
├── Remove deprecated code
├── Update documentation
└── Monitor stability
```

---

## PART 7: IMPLEMENTATION ROADMAP (NEXT 6 MONTHS)

### Month 1-2: Foundation
- ✅ Task model refactoring (unified)
- ✅ Task state machine service
- ✅ Database migrations (phase 1-2)
- ✅ Unit tests for state machine
- ✅ TaskWorkflowEngine service

### Month 2-3: MPFM Domain
- ✅ Consolidate MPFM services (3 → 1)
- ✅ MPFMCalculationEngine
- ✅ MPFMTestMetric model
- ✅ Integration tests

### Month 3-4: Lift Analysis Foundation
- ✅ LiftCurve, LiftCurvePoint models
- ✅ LiftCurveCalculationService (algorithm)
- ✅ Curve fitting & trending
- ✅ Integration tests

### Month 4-5: Dashboard & UI
- ✅ Department-specific dashboards
- ✅ Lift curve visualization
- ✅ Performance trending UI
- ✅ Task workflow UI

### Month 5-6: Analytics & Optimization
- ✅ Lift curve predictions
- ✅ Performance alerts
- ✅ Data quality dashboard
- ✅ Performance optimization
- ✅ Production deployment

---

## PART 8: VALIDATION & TESTING STRATEGY

### 8.1 Test Coverage Goals

```
Unit Tests:
├── Task state machine: 100% branch coverage
├── MPFM calculations: 100% coverage
├── Lift curve algorithm: 100% coverage
├── Domain services: 90%+ coverage
└── Value objects: 100% coverage

Integration Tests:
├── Task workflow end-to-end
├── MPFM test → Lift curve pipeline
├── Multi-source data aggregation
├── Authorization rules
└── SLA monitoring

Performance Tests:
├── Generate lift curve: <5s for 30 data points
├── Query 1000 tasks with filters: <500ms
├── Dashboard load: <2s
└── Real-time SLA check: <100ms per task

Data Validation:
├── Compare lift curves: New vs Legacy algorithm
├── Verify metric calculations (±0.1%)
├── Check state transitions
└── Audit trail completeness
```

---

## CONCLUSION

This refactoring plan:

1. **Eliminates architectural smells** - Clear domain boundaries, unified task engine, consolidated services
2. **Enables Lift Curve analytics** - Foundation for production surveillance and performance prediction
3. **Maintains backward compatibility** - No breaking changes, phased rollout
4. **Improves maintainability** - Explicit workflow, testable services, clear dependencies
5. **Supports scalability** - Multi-department architecture, modular domains, performance optimized

**Next Steps:**
1. Review with development team
2. Approve phased rollout schedule
3. Begin Phase 1 preparation
4. Establish testing & validation protocols

---

**Document Version:** 1.0
**Last Updated:** January 28, 2026
**Status:** Ready for Implementation Review
