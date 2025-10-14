## Phase 0 – Baseline Confirmation Checklist

Goal: validate current state of the `everb` application and database before designing schema or code changes. Complete every item and capture evidence (commands, outputs, screenshots) for the project log.

---

### 0.1 Database Restore & Snapshot
- [ ] Spin up a disposable MariaDB/MySQL instance (local Docker or test server).
- [ ] Load `everb_db_export.sql` and confirm restore with `SHOW TABLES` (expect 120+ tables).
- [ ] Export schema-only snapshot (`mysqldump --no-data`) and store as `baseline_schema.sql` for diffing later.
- [ ] Record DB server version, SQL mode, and character set defaults.

### 0.2 Module & Table Inventory
- [ ] Map purchase/sales modules: list controllers/models touching `product_purchase*`, `invoice*`, `product_information`, `stock` reports.
- [ ] Map livestock modules: list controllers/models using `livestocks`, `productions`, `feed_usages`, `vaccines`.
- [ ] Note shared utilities (helpers, libraries) for stock calculations or reporting.
- [ ] Highlight any direct SQL queries that bypass models (search for raw `SELECT * FROM product_purchase` etc.).

### 0.3 Units & Conversions Baseline
- [ ] Query `units` table; document each `unit_id`, `unit_name`, default quantity.
- [ ] Check if any existing controllers assume specific unit IDs (search for literals like `unit_id = 1`).
- [ ] Inventory any implicit conversions (e.g., code dividing by 30 for trays) to inform the conversion model.

### 0.4 Data Volume & Seed Records
- [ ] Count rows in key tables: `product_information`, `product_purchase_details`, `invoice_details`, `livestocks`, `productions`, `feed_usages`, `vaccines`.
- [ ] Identify tables populated only with sample/demo data; flag for cleanup pre-migration.
- [ ] Capture representative records (one per module) to design migration scripts and acceptance tests.

### 0.5 Reporting & API Touchpoints
- [ ] Review reports under `application/modules/report` for stock-related SQL; document expectations and existing filters.
- [ ] Inspect any API endpoints (if present) that expose stock quantities; assess how they compute inventory today.
- [ ] Note dependencies in other modules (accounting, HRM) that read stock or cost data.

### 0.6 Risks & Unknowns Log
- [ ] Create/extend a working log of open questions (costing method, negative stock handling, lot traceability).
- [ ] Flag required stakeholder decisions before Phase 1 (e.g., deprecating legacy stock report vs dual maintenance).
- [ ] Outline any tooling gaps (lack of tests, need for fixtures) discovered during baseline work.

Deliverables for Phase 0:
1. Restored database with schema snapshot.
2. Inventory documentation covering modules, tables, units, and data counts.
3. Risk/decision log feeding into Phase 1 design.

Do not proceed to schema changes until these items are signed off. 
