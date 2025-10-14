## Phase 3 - Internal Movements & Consumption Plan

Objective: ensure all non-sale inventory outflows (consumption, transfers, waste) are tracked through the stock movement ledger with full traceability and guardrails.

---

### 3.1 Transfers Between Locations
- [x] Catalogue current mechanisms (no prior automated transfer flow; baseline documented).
- [x] Design transfer workflow:
  - [x] Select source location, destination location.
  - [x] Choose lots/quantities, enforcing availability.
  - [x] Capture transfer notes/reference metadata.
- [x] Define movement reason codes (`TRANSFER_OUT`, `TRANSFER_IN`) for paired entries.
- [x] Draft service logic posting paired movements within a single DB transaction.
- [x] Plan UI changes (inventory transfer form) and necessary permissions.
- Notes: `InventoryLedger::recordTransfer()` with the new `inventory/transfers` module generates paired movements, auto-creates destination lots, and closes depleted lots when exhausted.

### 3.2 Consumption & Usage Logging
- [x] Map existing `feed_usages`, `vaccine_usages`, and similar tables; determine fields required for ledger entries.
- [x] Create movement reason catalog (`stock_movement_reasons`) including feed usage, vaccine usage, packaging consumption, maintenance etc.
- [x] Outline migration strategy to backfill historical totals into dated movement entries (aggregated per day or event).
- [x] Update relevant controllers/models to:
  - [x] Validate source lot availability.
  - [x] Post `movement_type = 'consumption'` entries with linked reason IDs.
  - [x] Record production batch or livestock group context where applicable.
- Notes: New `inventory/consumption` form posts ledger consumption via `InventoryLedger::recordConsumption()` with seeded reason codes, availability checks, and optional auto-allocation by product/location when the exact lot is unknown. Feed/vaccine flows now push into the ledger using product/location auto-allocation, with optional manual lot overrides when warehouse staff can identify the lot (per farm feedback, lots are often mixed; FIFO is the default fallback but overrides stay available).
- Context Linkage: Ledger/audit entries carry `reference_type` and `reference_id` to associate consumption with `livestock_groups` or `production_batches` where available, ensuring downstream analytics can slice usage per group/batch.


### 3.3 Waste/Spoilage & Adjustments
- Notes: inventory/waste form uses InventoryLedger::recordWaste() with override checkbox; attachments/QA triggers remain future work.
- [x] Define process to record wastage, breakage, expiry, mortality adjustments.
- [x] Require reason codes (override still allows optional attachments later).
- [x] Implement supervisor override workflow if resulting movement would cause negative stock.
- [ ] Consider integration with QA inspections (Phase 5) for automatic triggers.

### 3.4 Legacy Table Strategy
- [x] Decide whether to retain `feed_usages` and related tables:
  - Option A: mark as read-only views backed by new movement data.
  - [x] Option B: deprecate and migrate UI to ledger-backed tables entirely.
- [x] Document chosen approach and necessary migration scripts or view definitions.
- Notes: The existing livestock usage forms stay in place but now act as UI shells that post ledger movements. A CLI helper (`php index.php livestock resync_inventory_usage feed|vaccine|all`) backfills historical records into `stock_movements`, keeping legacy totals for reference while the ledger becomes the source of truth.

### 3.5 Controls & Auditing
- [x] Add audit events for transfers, consumption, and waste; capture who approved overrides.
- [x] Define notifications/alerts for excessive usage or repeated waste (ties into Phase 5 alerts).
 - [x] Plan analytics queries to compare expected vs actual feed usage per livestock group.
- Notes: `inventory_audit_events` and `inventory_notifications` tables capture each ledger posting, including override usage. Consumption/waste/transfer screens display the latest alert feed; analytics slices remain future work.
- Analytics Drafts:
  - Daily feed usage per group vs ration plan: aggregate `stock_movements` where `movement_type='consumption'` and `reason_code IN ('FEED_USAGE')`, join to livestock group ration targets via `reference_type/reference_id`.
  - Repeated waste by product/location: aggregate `inventory_audit_events` where `event_type='WASTE'` grouping by `product_id, location_id` over rolling 30 days; flag thresholds to drive notification severity.

### 3.6 Testing & Acceptance
- [x] Write acceptance tests covering:
  - Successful transfer with matching in/out entries.
  - Consumption reducing stock and preventing negative availability.
  - Waste event requiring supervisor approval when stock insufficient.
- [x] Validate reporting views reflect new movement types.
- [x] Update SOPs for warehouse/farm staff detailing new workflows.
