## Phase 2 - Inbound Flows Integration Plan

Objective: connect purchasing and production activities to the new core inventory model while preserving existing UI behaviour. All inflows must generate lots and stock movements with cost attribution.

---

### 2.1 Purchase Receipts
- [x] Audit current purchase workflow (controllers/views/models) to locate insert points for lot creation and movement posting.
- [x] Define new data fields required from UI: receiving location, lot metadata (supplier batch, expiry, grade), receipt cost breakdown.
- [x] Draft API/service interface: `InventoryLedger::recordPurchaseReceipt()` encapsulates lot creation and movement posting.
- [x] Plan modifications to `product_purchase` save logic to:
  - [x] Validate mandatory fields (supplier, date, location, invoice ref).
  - [x] Calculate landed cost per unit (line total ÷ quantity in base units).
  - [x] Create `stock_lots` records linked to purchase lines.
  - [x] Insert `stock_movements` rows with `movement_type = 'PURCHASE_RECEIPT'`.
- [x] Ensure rollback/transaction handling to maintain data integrity.
- Notes: implemented in `InventoryLedger.php` and `Purchase_model::insert_purchase()`; location selection surfaced in `add_purchase_form.php`.

### 2.2 Production Posting\n- [x] Review livestock production controllers and forms to capture product outputs, grades, units, and collection location.
- [x] Define `production_output_items` usage as the per-product detail store for each production record (leveraged for multi-output UI and ledger sync).
- [x] Establish conversion logic using Phase 1 `unit_conversions` to turn raw counts (e.g., eggs) into saleable units (trays) via `InventoryLedger`.
- [x] Update production submission pipeline to:
  - [x] Validate input rows and resolve unit conversions before posting.
  - [x] Create new lots for each finished good type.
  - [x] Post `stock_movements` with `movement_type = 'PRODUCTION_OUTPUT'`.
  - [x] Record mortality/damaged extras as `movement_type = 'PRODUCTION_LOSS'`.
- [ ] Decide how to capture production cost (feed allocation, labour) for COGS; log assumptions pending costing model decision.
- Notes: Production form now supports multiple output rows with dynamic add/remove controls and auto-calculated totals; edits hydrate from `production_output_items` and re-sync ledger entries.

### 2.3 Transformations (Optional in Phase 2, design groundwork)
- [ ] Identify existing code performing implicit conversions (e.g., packing eggs into trays) and document triggers.
- [ ] Define `stock_transforms` and `stock_transform_lines` schema drafts (consumes vs produces).
- [ ] Outline service interface `TransformService::execute($transformDefinition, $inputs)` to be implemented in Phase 3 if immediate need arises.

### 2.4 Validation & Safeguards
- [ ] Specify pre-checks for duplicate lot codes per purchase/production.
- [ ] Ensure negative stock checks run before posting any movement (especially adjustments or production losses).
- [ ] Describe audit logging requirements (link user ID, timestamp, source document).
- [ ] Plan unit/integration tests covering purchase receipt and production posting, including failure scenarios.

### 2.5 Reporting Adjustments
- [ ] Identify stock-related reports impacted by new inflow logic and note required updates (e.g., include lot/location info).
- [ ] Draft SQL/view changes to source stock-on-hand from `stock_movements`.

### 2.6 Acceptance Checklist
- [ ] Purchase receipt process creating lots/movements in test DB with expected cost per lot.
- [ ] Production posting pipeline generating saleable stock and logging losses.
- [ ] Regression tests confirming legacy purchase and production UI still operate.
- [ ] Documentation updated (user SOP, developer notes) describing new inputs and outputs.


