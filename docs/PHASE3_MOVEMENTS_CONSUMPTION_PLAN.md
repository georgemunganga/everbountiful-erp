## Phase 3 – Internal Movements & Consumption Plan

Objective: ensure all non-sale inventory outflows (consumption, transfers, waste) are tracked through the stock movement ledger with full traceability and guardrails.

---

### 3.1 Transfers Between Locations
- [ ] Catalogue current mechanisms (if any) moving stock between sheds/stores; document gaps.
- [ ] Design transfer workflow:
  - Select source location, destination location.
  - Choose lots/quantities, enforcing availability.
  - Optionally capture transfer reason/notes and transport cost.
- [ ] Define `movement_type` values: `transfer_out`, `transfer_in`.
- [ ] Draft service logic posting paired movements within a single DB transaction.
- [ ] Plan UI changes (modal/wizard) and necessary permissions.

### 3.2 Consumption & Usage Logging
- [ ] Map existing `feed_usages`, `vaccine_usages`, and similar tables; determine fields required for ledger entries.
- [ ] Create movement reason catalog (`stock_movement_reasons`) including feed usage, vaccine usage, packaging consumption, maintenance etc.
- [ ] Outline migration strategy to backfill historical totals into dated movement entries (aggregated per day or event).
- [ ] Update relevant controllers/models to:
  - Validate source lot availability.
  - Post `movement_type = 'consumption'` entries with linked reason IDs.
  - Record production batch or livestock group context where applicable.

### 3.3 Waste/Spoilage & Adjustments
- [ ] Define process to record wastage, breakage, expiry, mortality adjustments.
- [ ] Require reason codes and optional photo/attachment support.
- [ ] Implement supervisor override workflow if resulting movement would cause negative stock.
- [ ] Consider integration with QA inspections (Phase 5) for automatic triggers.

### 3.4 Legacy Table Strategy
- [ ] Decide whether to retain `feed_usages` and related tables:
  - Option A: mark as read-only views backed by new movement data.
  - Option B: deprecate and migrate UI to ledger-backed tables entirely.
- [ ] Document chosen approach and necessary migration scripts or view definitions.

### 3.5 Controls & Auditing
- [ ] Add audit events for transfers, consumption, and waste; capture who approved overrides.
- [ ] Define notifications/alerts for excessive usage or repeated waste (ties into Phase 5 alerts).
- [ ] Plan analytics queries to compare expected vs actual feed usage per livestock group.

### 3.6 Testing & Acceptance
- [ ] Write acceptance tests covering:
  - Successful transfer with matching in/out entries.
  - Consumption reducing stock and preventing negative availability.
  - Waste event requiring supervisor approval when stock insufficient.
- [ ] Validate reporting views reflect new movement types.
- [ ] Update SOPs for warehouse/farm staff detailing new workflows.
