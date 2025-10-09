## Phase 2 – Inbound Flows Integration Plan

Objective: connect purchasing and production activities to the new core inventory model while preserving existing UI behaviour. All inflows must generate lots and stock movements with cost attribution.

---

### 2.1 Purchase Receipts
- [ ] Audit current purchase workflow (controllers/views/models) to locate insert points for lot creation and movement posting.
- [ ] Define new data fields required from UI: receiving location, lot metadata (supplier batch, expiry, grade), receipt cost breakdown.
- [ ] Draft API/service interface: `PurchaseReceiptService::postReceipt($purchaseId)` returning created lot IDs and movement IDs.
- [ ] Plan modifications to `product_purchase` save logic to:
  - Validate mandatory fields (supplier, date, location, invoice ref).
  - Calculate landed cost per unit (base cost + freight + adjustments).
  - Create `stock_lots` records linked to purchase lines.
  - Insert `stock_movements` rows with `movement_type = 'purchase_receipt'`.
- [ ] Ensure rollback/transaction handling to maintain data integrity.

### 2.2 Production Posting
- [ ] Review livestock production controllers and forms to capture product outputs, grades, units, and collection location.
- [ ] Define `production_batches` table or view mapping to `productions` records with per-product detail.
- [ ] Establish conversion logic using Phase 1 `unit_conversions` to turn raw counts (e.g., eggs) into saleable units (trays).
- [ ] Update production submission pipeline to:
  - Validate input units against product conversion rules.
  - Create new lots for each finished good type.
  - Post `stock_movements` with `movement_type = 'production_output'`.
  - Record mortality/damaged extras as `movement_type = 'production_loss'`.
- [ ] Decide how to capture production cost (feed allocation, labour) for COGS; log assumptions pending costing model decision.

### 2.3 Transformations (Optional in Phase 2, design groundwork)
- [ ] Identify existing code performing implicit conversions (e.g., packing eggs into trays) and document triggers.
- [ ] Define `stock_transforms` and `stock_transform_lines` schema drafts (consumes vs produces).
- [ ] Outline service interface `TransformService::execute($transformDefinition, $inputs)` to be implemented in Phase 3 if immediate need arises.

### 2.4 Validation & Safeguards
- [ ] Specify pre-checks for duplicate lot codes per purchase/production.
- [ ] Ensure negative stock checks run before posting any movement (especially adjustments or production losses).
- [ ] Describe audit logging requirements – link user ID, timestamp, source document.
- [ ] Plan unit/integration tests covering purchase receipt and production posting, including failure scenarios.

### 2.5 Reporting Adjustments
- [ ] Identify stock-related reports impacted by new inflow logic and note required updates (e.g., include lot/location info).
- [ ] Draft SQL/view changes to source stock-on-hand from `stock_movements`.

### 2.6 Acceptance Checklist
- [ ] Purchase receipt process creating lots/movements in test DB with expected cost per lot.
- [ ] Production posting pipeline generating saleable stock and logging losses.
- [ ] Regression tests confirming legacy purchase and production UI still operate.
- [ ] Documentation updated (user SOP, developer notes) describing new inputs and outputs.
