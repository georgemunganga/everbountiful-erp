## Unified Inventory + Livestock Module Integration Plan (Pre–Phase 4)

Objective: merge inventory and livestock concerns into a single, cohesive module that tracks stock at the location level (warehouse, shed, bin) without relying on lots, while preserving livestock domain workflows and providing auditability, notifications, and analytics.

### Guiding Principles
- Location-centric stock truth: movements reference `product_id` + `location_id`; `lot_id` becomes optional/legacy.
- Single source of truth: every stock change is a ledger entry; UI writes via service helpers only.
- Domain-first flows: livestock screens and processes remain, augmented by inventory guardrails and audit.
- Traceability without lots: use `movement_date`, `reason_code`, `grade/age` metadata and `reference_type/reference_id` to maintain lineage.

### Scope
- Integrate inventory consumption, waste, and transfer flows directly into livestock module screens.
- Unify controllers under one module (proposed: `stock`), while keeping livestock entities and reporting.
- Align schema and services to support location-based movements and costing.

### Data Model Shifts
- Ledger: continue using `stock_movements` with strong indexing on `(product_id, location_id, movement_date)`.
- Lots: set `lot_id` nullable and treat as deprecated; do not require for new postings.
- Costing: move to location-level moving average (product+location) or global product moving average. Pull cost from latest purchase/production postings applicable to the location.
- Metadata: extend movement payloads (not schema at this step) to carry `grade`, `age_days`, and `reference_type/reference_id` (e.g., `livestock_group`, `production_batch`).
- Reasons: reuse `stock_movement_reasons` (FEED_USAGE, VACCINE_USAGE, WASTE, TRANSFER_IN/OUT, etc.).
- Audit/Notifications: continue with `inventory_audit_events` and `inventory_notifications` for all postings.

### Service Layer (API)
- Keep `InventoryLedger` as the stock gatekeeper; accept payloads with `{product_id, location_id}` (no lot required) and optional overrides.
- Consumption: `recordConsumption({product_id, location_id, quantity, unit_id?, reason_code, reference_type, reference_id, allow_negative?})`.
- Waste: `recordWaste({product_id, location_id, quantity, reason_code, allow_negative?, reference_*})`.
- Transfer: `recordTransfer({from_location_id, to_location_id, product_id?, quantity, reference_*})`. If product omitted, infer from source context in UI.
- AuditTrail: `recordEventWithNotification(event_type, eventPayload, notificationPayload)` used by all flows.

### UI/UX Integration
- Livestock Usage Forms: replace batch/lot pickers with Location picker and optional grade/age display; enable auto-allocation by location.
- Livestock Waste: require reason codes; show override workflow when stock insufficient.
- Internal Transfers: provide a simple “Move stock” form (source location → destination location, product, quantity).
- Notifications: embed the inventory notification feed on key livestock screens.
- Sidebar: expose unified “Inventory” actions inside livestock menu, or move both under a new “Stock” group.

### Permissions
- Reuse existing permission checks; introduce fine-grained keys for `stock_consumption`, `stock_waste`, `stock_transfer`, and `stock_override` (supervisor).
- Maintain livestock view/edit permissions for domain entities.

### Migration Strategy
- Schema: allow `stock_movements.lot_id` to be NULL; ensure indexes on `(product_id, location_id, movement_date)`; no destructive changes at this step.
- Legacy Tables: retain `feed_usages` and `vaccine_usages` with `inventory_product_id` for mapping.
- Backfill: run `php index.php livestock resync_inventory_usage feed|vaccine|all` to populate ledger without lots; set `reference_type`/`reference_id` to livestock group/batch where available.
- Views: optionally create read-only views that expose historical lot-based rows mapped to location-centric records.

### Reporting & Analytics
- Stock on hand: aggregate `stock_movements` by product+location; provide age/grade slices from metadata.
- Feed usage vs plan: group by `reference_type=livestock_group` and compare to ration targets.
- Waste patterns: trend by product/location; raise alerts on repeated events over thresholds.
- Transfers: trace movement chains across locations; confirm no negative stock unless approved.

### Testing & Acceptance
- Unit/Integration: verify location-based postings for consumption, waste, and transfers; audit and notifications emitted.
- Negative stock controls: block standard postings; allow supervisor overrides with audit notes.
- Reporting: confirm location-based stock snapshots and analytics accuracy.
- UI: validate new pickers and feeds on livestock forms.

### Rollout Steps
1) Apply audit/notification tables (done) and ensure `stock_movements.lot_id` is nullable.
2) Update livestock forms to call InventoryLedger with product+location payloads.
3) Embed notifications in livestock screens.
4) Run CLI backfill for usage into the ledger.
5) Validate stock snapshots and alerts; adjust severity thresholds.
6) Plan Phase 4 changes with location-based allocation in sales (no lot selection).

### Risks & Mitigations
- Loss of batch-level traceability: mitigate via metadata fields and `reference_type/reference_id` links to production; keep optional batch codes if needed.
- Cost accuracy without lots: adopt moving average and ensure purchase/production postings update location-level cost.
- User retraining: update SOPs focusing on “pick by location” and override policies.

This plan establishes a unified, location-centric stock model that integrates inventory controls directly into livestock workflows, paving the way for Phase 4 sales allocation by location without lots.