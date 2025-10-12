## Core Inventory Model Snapshot (Phase 1)

The following tables implement the baseline schema for unified stock management. They are included in `install/sql/install.sql` and in the incremental patch `install/sql/patches/20251009_core_inventory_phase1.sql`.

### stock_locations
- Represents any physical or logical place stock can live (fields, sheds, stores, cold rooms).
- Key columns: `location_code` (unique human-friendly code), `location_type`, optional `parent_id` for hierarchy, audit metadata.
- Self-referencing FK (`parent_id`) enables nesting locations (e.g., farm -> shed -> bin).

### product_units
- Lists every unit of measure a product can use along with conversion info.
- Columns: `conversion_factor` (relative to base unit), boolean flags for base/purchase/sales defaults.
- FK to `units.unit_id`; unique constraint per product/unit pair prevents duplicates.

### unit_conversions
- Optional overrides for special product conversions or global unit translations.
- Uses `product_id` (empty string for global conversion) plus `from_unit_id`, `to_unit_id`, and `multiply_factor`.
- Dual FK to `units` keeps unit references consistent.

### stock_lots
- Stores metadata for each receipt/production batch: `lot_code`, origin references, grade, expiry, initial quantity, and location.
- FKs link to `stock_locations` and `units`, allowing traceability without touching sales tables yet.

### stock_movement_reasons
- Controlled vocabulary describing why a movement occurred, with direction tagging (`in`, `out`, `adjust`).
- Seeded with baseline records: `INITIAL_BALANCE`, `MANUAL_ADJUSTMENT`, `UNCLASSIFIED_OUT`, plus purchase/production reasons in Phase 2.
- Supports enable/disable toggles and audit columns for governance.

### stock_movements
- Central ledger capturing every stock change, referencing lot, location, unit, and reason.
- Stores both quantity and cost (per unit and total) for future COGS calculations.
- Indexed for chronological queries and traceability (product, lot, reference keys).

### inventory_audit_events
- Append-only log automatically written alongside ledger activity.
- Captures event type (`TRANSFER`, `CONSUMPTION`, `WASTE`, etc.), reference handles, product/lot/location context, quantity, reason code, plus JSON metadata (override flags, movement IDs).
- Nullable FKs to lots, locations, and units allow recording high-level events even when the precise lot is unknown.

### inventory_notifications
- Stores surfaced alerts tied to audit events.
- Tracks severity (`info`, `warning`), read state, and mirrors the originating reference so UI modules can show actionable banners.
- FK to `inventory_audit_events` lets consumers fetch full context on demand.

### Relationships Overview
- `stock_movements` + `stock_lots` + `stock_locations` create the chain from ledger entry to physical place and product.
- `inventory_audit_events` references the same trio for traceability, while `inventory_notifications` fan out into user-facing alerts.
- `product_units` and `unit_conversions` provide the unit metadata the ledger will rely on in later phases.
- All new tables use InnoDB with `utf8mb4` to support referential integrity and multi-language names.

### Incremental Patch Usage
Run `install/sql/patches/20251009_core_inventory_phase1.sql` on an existing database to create the new tables and seed movement reasons without disturbing current data. New installations automatically include the tables via the updated installer.

This schema completes the Phase 1 deliverable of establishing the foundational data structures needed before wiring purchases, production, and sales into the ledger.

## Movement Reasons Added in Phases 2-3

Subsequent phases extend `stock_movement_reasons` via the application (using `InventoryLedger::ensureReason()`):

| Reason Code       | Direction | Description                      |
| ----------------- | --------- | -------------------------------- |
| `PURCHASE_RECEIPT`| in        | Stock received from supplier     |
| `PRODUCTION_OUTPUT` | in     | Finished goods created in production |
| `PRODUCTION_LOSS` | out       | Losses logged against production |
| `TRANSFER_OUT`    | out       | Stock moved out of a location    |
| `TRANSFER_IN`     | in        | Stock received into a location   |
| `FEED_USAGE`      | out       | Feed consumption from inventory  |
| `VACCINE_USAGE`   | out       | Vaccine usage                    |
| `PACKAGING`       | out       | Packaging materials consumption  |
| `MAINTENANCE`     | out       | Maintenance related consumption  |
| `GENERAL_CONSUME` | out       | Miscellaneous consumption        |
| `WASTE`           | out       | General waste/spoilage           |
| `BREAKAGE`        | out       | Breakage or damage               |
| `EXPIRY`          | out       | Expired or stale stock           |
| `MORTALITY`       | out       | Livestock mortality adjustments  |
| `QUALITY_DROP`    | out       | Quality downgrade adjustments    |

These codes are created automatically when first used by the ledger helpers, keeping the schema compatible with earlier installations.

### Legacy Usage Tables
- `feed_usages` and `vaccine_usages` now include an `inventory_product_id` column so each entry can be mapped to ledger postings.
- The livestock module continues to store summary totals (for historical reporting) while immediately calling `InventoryLedger` to create `stock_movements`.
- Run `php index.php livestock resync_inventory_usage feed|vaccine|all` during rollout to backfill existing usage rows into the ledger and populate the new audit/notification streams.
