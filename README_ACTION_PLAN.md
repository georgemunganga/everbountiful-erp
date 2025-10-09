## Unified Inventory & Livestock Integration – Action Plan

Context: The current `everb` database keeps livestock activity (`livestocks`, `productions`, `feed_usages`, `vaccines`) isolated from the sales/purchase inventory tables (`product_information`, `product_purchase_details`, `invoice_details`). Lots, locations, and a stock movement ledger are missing, so production never becomes saleable stock and consumptions are not reflected. The steps below sequence the work needed to deliver the checklist captured in `analyse.txt`, with validation gates before any code is written.

---

### Phase 0 – Baseline Confirmation
- [ ] Restore the latest `everb_db_export.sql` to a disposable database and snapshot the schema (ensures future diffs are precise).
- [ ] Catalogue existing tables/APIs touching stock (`product_*`, `invoice_*`, stock reports, livestock controllers) and note coupling points.
- [ ] Confirm `units` catalogue (already populated) and map existing IDs to their intended use (pieces, trays, etc.).
- [ ] Document current data volume and seed records to design migrations with zero data loss.

### Phase 1 – Core Inventory Model
- [ ] Introduce **locations**: design `stock_locations` (fields, sheds, stores) and map current sheds to it; validate migration path for existing sheds data.
- [ ] Define **products & conversions** enhancements: add `product_units` / `unit_conversions` so agents can convert (for example, 30 eggs → 1 tray); reuse rows in `units`.
- [ ] Add **lot/batch master**: `stock_lots` capturing source operation (purchase, production, transform), location, date, grade; ensure it can reference both purchases and productions.
- [ ] Create a single **stock movement ledger**: `stock_movements` with fields (movement_id, product_id, lot_id, location_id, qty_in, qty_out, unit_id, cost, movement_type, source_table, source_id, created_by, created_at). Enforce “no stock mutation without movement”.
- [ ] Plan migrations and seed data to ensure existing on-hand balances (if any) are reverse engineered into opening movements.

### Phase 2 – Inbound Flows
- **Purchases**
  - [ ] Extend purchase receipt UI/backend to generate lots, post `qty_in` movements, and store landed cost per lot.
  - [ ] Add validation for mandatory supplier, delivery date, location, lot metadata.
  - [ ] Regression check: confirm existing purchase reports still balance when movements drive stock totals.
- **Production**
  - [ ] Map `productions` entries to new `production_batches` (per product output, grade, unit, cost allocation).
  - [ ] Allow splitting a production run into multiple products (for example, egg trays and seconds).
  - [ ] Post corresponding `qty_in` movements referencing production lots; unit conversions applied via the conversion table.
  - [ ] Capture mortality/wastage as `qty_out` movements with reason codes.
- **Transformations**
  - [ ] Design recipes/work orders table (`stock_transforms`, `stock_transform_lines`) that consumes specific lots/qty_out and produces new lots/qty_in.
  - [ ] Enforce variance tracking (expected versus actual) with audit trail.

### Phase 3 – Internal Movements & Consumption
- [ ] Implement **transfers** between locations: wizard that picks source lots, quantity, destination location; posts paired movements (out and in).
- [ ] Model **consumption** (feed, packaging, vaccines) as stock movements rather than aggregate columns. Migrate existing `feed_usages` totals into historical movements and mark the table as legacy read-only or refactor controllers to use the ledger.
- [ ] Add **waste/spoilage** event type with reason codes and supervisor override for negative stock blocks.

### Phase 4 – Outbound Sales & Returns
- [ ] Update sales order/invoice workflow to allocate lots (default FIFO with manual override). Ensure cost of goods pulls from the associated lot’s moving-average or standard cost.
- [ ] Modify stock checks to read from the movement ledger, blocking sales that would create negative availability.
- [ ] Handle returns by reversing the original lot movement or creating a new return lot when quality is compromised.
- [ ] Propagate lot references into accounting entries for traceability.

### Phase 5 – Reporting, Alerts, & Audit
- [ ] Build stock-on-hand views aggregating `stock_movements` by product, lot, location; expose filters for grade, age, status.
- [ ] Implement KPI dashboards (egg lay rate, breakage, crop yields) combining production data with movements.
- [ ] Configure alert rules (low stock thresholds, aging lots, abnormal usage ratios) and notification plumbing.
- [ ] Ensure every movement/action is audit logged (user, timestamp, before/after) and supports “reverse and repost” corrections rather than hard edits.

### Phase 6 – Security, Roles, and UX
- [ ] Review existing roles; extend permissions for new operations (production posting, transforms, transfers).
- [ ] Add approval steps for risky actions (cost overrides, negative stock overrides, lot disposal).
- [ ] Outline critical modals/wizards: Harvest, Egg Collection, Grading, Production-to-Inventory, Transfer, Waste, Lot Trace.
- [ ] Plan minimal mobile/offline capture forms for sheds/fields, aligned with sync constraints.

### Phase 7 – Validation & Rollout
- [ ] Define automated test coverage: unit tests for movement balancing, integration tests for sales/purchase flows, and migration smoke tests.
- [ ] Prepare migration scripts with back-out strategy; run on staging clone, reconcile stock balances against pre-migration baseline.
- [ ] Draft operator SOP/training docs and quick reference for new workflows.
- [ ] Schedule go-live with inventory freeze window; monitor KPIs and alerts post-deploy.

---

**Dependencies & Open Questions**
1. Confirm whether legacy stock reports must stay untouched or can be replaced with ledger-driven versions.
2. Decide if existing livestock tables (`livestocks`, `productions`, `feed_usages`, and related tables) should be normalized into the new ledger or wrapped by services that post movements.
3. Clarify costing method (moving average versus standard versus FIFO) and accounting integration requirements.

This plan stays separate from `analyse.txt` as requested and serves as the implementation roadmap once each validation gate is cleared.
