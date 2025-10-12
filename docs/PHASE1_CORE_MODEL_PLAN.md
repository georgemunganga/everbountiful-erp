## Phase 1 – Core Inventory Model Design

Objective: define the foundational structures (locations, conversions, lots, movements) that enable unified inventory management without altering application behaviour yet. Produce ERDs, migration outlines, and validation criteria ready for implementation.

### Current Status
- [x] Core tables & seed data captured in `install/sql/install.sql` and `install/sql/patches/20251009_core_inventory_phase1.sql`.
- [x] Schema summary documented in `docs/CORE_INVENTORY_SCHEMA.md`.
- [x] Legacy bootstrap script created and executed (`install/sql/patches/20251009_core_inventory_phase1_seed.sql`) to map sheds and product units.
- [x] ERD and supporting documentation stored in `docs/PHASE1_DELIVERABLES.md` (pending stakeholder sign-off noted there).
- [x] Migration dry-run notes and validation queries logged in `docs/PHASE1_DELIVERABLES.md`.
- [x] Repository/service interfaces outlined in `docs/PHASE1_DELIVERABLES.md`.

---

### 1.1 Domain Analysis & ERD
- [x] Document required entities and relationships for locations, product-unit links, lots, movements, and metadata (`docs/CORE_INVENTORY_SCHEMA.md`, `docs/PHASE1_DELIVERABLES.md`).
- [x] Produce an ERD highlighting keys, cardinality, and FK constraints (`docs/PHASE1_DELIVERABLES.md`).
- [ ] Validate design with stakeholders (operations, accounting) focusing on traceability and costing expectations.

### 1.2 Table Specifications
- [x] Draft DDL for each new table including columns, keys, and constraints (`install/sql/install.sql`, patch scripts).
- [x] Define naming conventions for lot/location codes (see bootstrap script and ERD notes).
- [x] Capture audit requirements (`created_at`, `updated_at`, `created_by`, `updated_by` in schema).

### 1.3 Migration Strategy
- [x] Map existing data (`sheds` ? `stock_locations`, `product_information` ? `product_units`) via `install/sql/patches/20251009_core_inventory_phase1_seed.sql`.
- [x] Design migration order and rollback (documented in patch scripts and deliverables).
- [x] Prepare post-migration validation queries (listed in `docs/PHASE1_DELIVERABLES.md`).

### 1.4 Integration Touchpoints
- [x] Identify backend services/models that will use new tables (InventoryLocationRepository, ProductUnitService, LotRepository, StockMovementService defined in `docs/PHASE1_DELIVERABLES.md`).
- [x] Flag code areas for refactor in later phases (stock math, reporting) – tracked in README action plan and risks section.

### 1.5 Testing & Tooling Prep
- [x] Outline required migration/repository tests and fixtures (`docs/PHASE1_DELIVERABLES.md`, Testing & Tooling section).
- [x] Specify seed data usage and automation approach (same section; future scripts noted).

### 1.6 Acceptance Checklist
- [ ] ERD signed off by stakeholders.
- [x] DDL reviewed for indexes and data types (implemented in schema and validated via DESCRIBE checks).
- [x] Migration plan validated on restored database (live execution on `everb` + validation queries).
- [x] Risks and dependencies captured and forwarded to Phase 2 planning (`docs/PHASE1_DELIVERABLES.md`, risks section).

Deliverables on record:
1. ERD and table spec documentation (`docs/CORE_INVENTORY_SCHEMA.md`, `docs/PHASE1_DELIVERABLES.md`).
2. Executable SQL migration scripts (`install/sql/patches/20251009_core_inventory_phase1.sql`, `install/sql/patches/20251009_core_inventory_phase1_seed.sql`).
3. Updated risk/dependency log (`docs/PHASE1_DELIVERABLES.md` section 5).
