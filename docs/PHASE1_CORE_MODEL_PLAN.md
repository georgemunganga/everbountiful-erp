## Phase 1 – Core Inventory Model Design

Objective: define the foundational structures (locations, conversions, lots, movements) that enable unified inventory management without altering application behaviour yet. Produce ERDs, migration outlines, and validation criteria ready for implementation.

---

### 1.1 Domain Analysis & ERD
- [ ] Document required entities and relationships for:
  - Locations (`stock_locations`)
  - Product-to-unit relationships (`product_units`, `unit_conversions`)
  - Lots/batches (`stock_lots`)
  - Stock movements (`stock_movements`)
  - Supporting metadata (movement reasons, status flags)
- [ ] Produce an ERD highlighting keys, cardinality, inferred FK constraints.
- [ ] Validate design with stakeholders (operations, accounting) focusing on traceability and costing expectations.

### 1.2 Table Specifications
- [ ] Draft DDL for each new table including:
  - Columns, data types, defaults, nullability
  - Primary keys and indexes
  - Foreign key definitions and cascade rules
  - Enumerations (movement types, statuses) stored as lookup tables where appropriate
- [ ] Define naming conventions for lot codes and location codes.
- [ ] Capture audit requirements (timestamps, `created_by`, `updated_by`).

### 1.3 Migration Strategy
- [ ] Assess existing data that needs mapping:
  - `sheds` → initial `stock_locations`
  - `product_information` → default `product_units`
  - Seed lot and movement entries where current stock exists (likely zero, but confirm)
- [ ] Design migration scripts/pseudocode:
  - Table creation order respecting FK dependencies
  - Backfill logic for initial data
  - Rollback plan (drop tables) while database still in baseline state
- [ ] Prepare data validation queries to run post-migration (row counts, FK integrity).

### 1.4 Integration Touchpoints
- [ ] Identify backend services/models that will eventually interact with new tables.
- [ ] Draft adapters or repositories interfaces (pseudo) for later implementation.
- [ ] Flag code areas requiring refactor in subsequent phases (stock maths, reports).

### 1.5 Testing & Tooling Prep
- [ ] Outline unit/integration tests needed for schema changes (migration tests, repository tests).
- [ ] Specify fixtures or seed data supporting those tests.
- [ ] Plan automation scripts (e.g., Laravel migrations wrapper or custom CLI) to execute schema changes reliably.

### 1.6 Acceptance Checklist
- [ ] ERD signed off by stakeholders.
- [ ] DDL reviewed for performance and maintainability (indexes, data types).
- [ ] Migration plan validated on restored Phase 0 database snapshot.
- [ ] Risks/Open items documented and fed into Phase 2 plan.

Deliverables:
1. ERD and table spec document (PDF/Image/Markdown).
2. Draft SQL migration scripts (not executed yet).
3. Updated risk and dependency log referencing Phase 1 decisions.
