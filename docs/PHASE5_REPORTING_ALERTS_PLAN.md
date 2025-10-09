## Phase 5 – Reporting, Alerts, & Audit Plan

Objective: leverage the unified stock ledger to deliver actionable insights, proactive alerts, and comprehensive audit trails for compliance.

---

### 5.1 Stock & Lot Reporting
- [ ] Design SQL/views aggregating `stock_movements` into stock-on-hand snapshots by product, lot, location.
- [ ] Define additional views for:
  - Lot aging (days on hand, expiry proximity).
  - Grade/quality distribution.
  - Production vs sales comparison.
- [ ] Update existing stock reports to use new views or replace them entirely; document any legacy reports retained for historical comparison.

### 5.2 KPI Dashboards
- [ ] Identify core KPIs: egg lay rate, crack/breakage rate, crop yield per field, feed conversion ratio (FCR), sales margin by product.
- [ ] Map data sources needed (production, consumption, sales, costs) and specify calculation formulas.
- [ ] Design dashboard wireframes/widgets; note drill-down requirements.
- [ ] Plan caching/aggregation strategy for heavy metrics (daily snapshots vs real-time).

### 5.3 Alerts & Notifications
- [ ] Build alert rules catalog: low stock thresholds, perishable lot aging, abnormal consumption, variance breaches.
- [ ] Define alert evaluation engine (scheduled job or event-driven).
- [ ] Specify delivery channels: in-app notifications, email, SMS (based on existing `sms_settings`).
- [ ] Outline override flow for blocked operations (e.g., selling expired lots) requiring supervisor confirmation.

### 5.4 Audit & Traceability
- [ ] Ensure every stock-affecting action writes to an audit log with before/after state, user, timestamp.
- [ ] Develop traceability report linking:
  - Sale → allocated lot → source production/purchase → supplier/field/shed.
  - Lot → all downstream movements (transfers, sales, waste).
- [ ] Plan data retention policies and archival strategy for movement and audit logs.

### 5.5 Testing & Validation
- [ ] Define test cases to validate report accuracy (cross-check totals against raw movement sums).
- [ ] Implement automated checks ensuring alert thresholds trigger as expected.
- [ ] Conduct performance testing on heavy reports; index tuning if required.
- [ ] Gather stakeholder sign-off on KPI definitions and report layouts.
