## Phase 4 – Outbound Sales & Returns Plan

Objective: integrate location-aware stock depletion into sales workflows (no lots), ensuring cost of goods calculation, traceability via location and metadata, and proper handling of returns or write-offs.

---

### 4.1 Sales Order & Invoice Allocation
- [ ] Review current invoicing flow (`invoice`, `invoice_details`) to map entry points for location selection.
- [ ] Define default allocation strategy (FIFO within location) with ability for authorized users to override by age/grade or specific stock pools.
- [ ] Extend UI to capture:
  - Requested ship/issue location.
  - add Location of where we are picking products from  replacing batch selection (picker modal with availability, age, grade) — no lot selection.
  - Override reason when departing from FIFO.
- [ ] Update backend to:
  - Reserve quantities during order confirmation (optional hold table).
  - Post `stock_movements` with `movement_type = 'sale_dispatch'` once invoice finalized.
  - Record cost per line item using moving-average cost per product+location (or globally per product when location-specific cost is unavailable).

### 4.2 Cost of Goods & Accounting Hooks
- [ ] Determine COGS methodology (moving average per product+location) consistent with Phase 1 decisions.
- [ ] Map flow from stock movements to accounting entries (`acc_transaction`, `acc_vaucher`).
- [ ] Update financial integration layer to pull moving-average location cost and generate GL postings (inventory credit, COGS debit).
- [ ] Document reconciliation process aligning sales data with accounting module.

### 4.3 Returns & Exchanges
- [ ] Define return workflows for:
  - Sellable returns (go back into stock).
  - Unsellable returns (write-off or send to rework).
- [ ] Implement logic to reverse or create new movements:
  - `return_to_stock` crediting back the issuing location (store metadata of original source where available).
  - `return_waste` generating disposal movements.
- [ ] Update UI to capture return reason, condition, and restocking decision.
- [ ] Ensure financial adjustments (credit note, reversal of COGS) are handled.

### 4.4 Negative Stock Prevention
- [ ] Enforce stock availability checks at order entry and dispatch.
- [ ] Provide supervisor override workflow with logged justification when temporary negatives must be allowed.
- [ ] Add alert/notification for attempted negative allocations.

### 4.5 Reporting & Analytics
- [ ] Update sales and margin reports to include location information and cost of goods derived from movements.
- [ ] Provide traceability reports linking completed sales to source locations and upstream production/purchase metadata.
- [ ] Plan dashboards showing sales velocity vs production output for forecasting.

### 4.6 Testing & Acceptance
- [ ] Define integration tests covering:
  - Standard sale with FIFO (within location) producing accurate stock reduction and COGS.
  - Manual allocation override (location/age/grade) with permission checks.
  - Return processed back to inventory and waste scenarios.
- [ ] Reconcile stock levels before and after test transactions.
- [ ] Update SOPs for sales and warehouse teams on new allocation steps.
