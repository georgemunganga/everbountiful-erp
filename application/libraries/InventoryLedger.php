<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * InventoryLedger
 *
 * Helper library to centralise stock lot creation and stock movement posting.
 */
class InventoryLedger
{
    /** @var CI_Controller */
    protected $ci;

    /** @var InventoryAuditTrail|null */
    protected $auditTrail = null;

    /** @var array<string,int> */
    protected $reasonCache = [];

    /** @var array<int,array> */
    protected $productUnitCache = [];

    /** @var int|null */
    protected $defaultLocationId = null;

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->database();
        $this->ci->load->library('InventoryAuditTrail');
        if (isset($this->ci->inventoryaudittrail) && $this->ci->inventoryaudittrail instanceof InventoryAuditTrail) {
            $this->auditTrail = $this->ci->inventoryaudittrail;
        }
    }

    /**
     * Resolve a valid stock location id. Returns default location if the supplied id is invalid.
     */
    public function resolveLocationId($locationId = null)
    {
        $locationId = (int) $locationId;
        if ($locationId > 0 && $this->locationExists($locationId)) {
            return $locationId;
        }
        return $this->getDefaultLocationId();
    }

    /**
     * Resolve a shed's location id (maps to the SHDxxxx location code seeded in stock_locations).
     */
    public function resolveShedLocationId($shedId)
    {
        $shedId = (int) $shedId;
        if ($shedId <= 0) {
            return $this->getDefaultLocationId();
        }
        $code = sprintf('SHD%04d', $shedId);
        $row = $this->ci->db->select('id')
            ->from('stock_locations')
            ->where('location_code', $code)
            ->where('is_active', 1)
            ->limit(1)
            ->get()
            ->row();
        if ($row) {
            return (int) $row->id;
        }
        return $this->getDefaultLocationId();
    }

    /**
     * Fetch all active stock locations (id, code, name, type).
     */
    public function getActiveLocations()
    {
        return $this->ci->db->select('id, location_code, location_name, location_type')
            ->from('stock_locations')
            ->where('is_active', 1)
            ->order_by('location_name', 'asc')
            ->get()
            ->result_array();
    }

    /**
     * Record a purchase receipt: create lot + stock movements and return identifiers.
     *
     * @param array $payload {
     *   string product_id,
     *   int location_id,
     *   int unit_id,
     *   float quantity,
     *   string|null batch_no,
     *   string|null expiry_date,
     *   string purchase_id,
     *   string purchase_detail_id,
     *   string purchase_date,
     *   float total_amount,
     *   float discount_amount,
     *   float vat_amount,
     *   int|null created_by
     * }
     * @return array{lot_id:int,movement_id:int}|false
     */
    public function recordPurchaseReceipt(array $payload)
    {
        $productId   = isset($payload['product_id']) ? trim($payload['product_id']) : '';
        $locationId  = (int) ($payload['location_id'] ?? 0);
        $unitId      = (int) ($payload['unit_id'] ?? 0);
        $quantity    = (float) ($payload['quantity'] ?? 0);
        if ($productId === '' || $locationId <= 0 || $quantity <= 0) {
            return false;
        }

        $baseUnit    = $this->getBaseUnitContext($productId);
        if (!$baseUnit) {
            return false;
        }

        if ($unitId <= 0) {
            $unitId = $baseUnit['unit_id'];
        }

        $quantityBase = $this->convertToBaseQuantity($productId, $unitId, $quantity);
        $lotCode      = $payload['batch_no'] ?? null;
        $lotId        = $this->ensureLot([
            'lot_code'              => $lotCode ?: $this->generateLotCode('PUR'),
            'product_id'            => $productId,
            'base_unit_id'          => $baseUnit['unit_id'],
            'location_id'           => $locationId,
            'initial_quantity'      => $quantityBase,
            'source_type'           => 'purchase',
            'source_reference'      => $payload['purchase_id'] ?? null,
            'source_reference_line' => $payload['purchase_detail_id'] ?? null,
            'production_date'       => $this->safeDate($payload['purchase_date'] ?? null),
            'expiry_date'           => $this->safeDate($payload['expiry_date'] ?? null),
            'notes'                 => null,
        ]);

        if (!$lotId) {
            return false;
        }

        $reasonId     = $this->getReasonId('PURCHASE_RECEIPT');
        if (!$reasonId) {
            return false;
        }

        $costTotal    = (float) ($payload['total_amount'] ?? 0);
        $costPerUnit  = $quantityBase > 0 ? ($costTotal / $quantityBase) : 0;
        $movementDate = $this->safeDate($payload['purchase_date'] ?? null);

        $movementId = $this->createMovement([
            'movement_date'    => $movementDate,
            'product_id'       => $productId,
            'lot_id'           => $lotId,
            'location_id'      => $locationId,
            'unit_id'          => $baseUnit['unit_id'],
            'quantity_in'      => $quantityBase,
            'quantity_out'     => 0,
            'cost_per_unit'    => $costPerUnit,
            'cost_total'       => $costTotal,
            'reason_id'        => $reasonId,
            'reference_type'   => 'purchase',
            'reference_id'     => $payload['purchase_id'] ?? null,
            'reference_line_id'=> $payload['purchase_detail_id'] ?? null,
            'narration'        => sprintf('Purchase receipt %s', $payload['purchase_id'] ?? ''),
            'created_by'       => $payload['created_by'] ?? null,
        ]);

        if (!$movementId) {
            return false;
        }

        return [
            'lot_id'       => (int) $lotId,
            'movement_id'  => (int) $movementId,
        ];
    }

    /**
     * Refresh a production batch output and post stock movements.
     *
     * @param array $payload contains:
     *   production_id, product_id, unit_id, quantity,
     *   location_id, production_date, mortality_qty, damaged_qty,
     *   extras_qty, created_by
     */
    public function recordProductionOutput(array $payload, $clearExisting = true)
    {
        $productionId = (int) ($payload['production_id'] ?? 0);
        $productId    = isset($payload['product_id']) ? trim($payload['product_id']) : '';
        $unitId       = (int) ($payload['unit_id'] ?? 0);
        $quantity     = (float) ($payload['quantity'] ?? 0);
        $locationId   = (int) ($payload['location_id'] ?? 0);

        if ($productionId <= 0 || $productId === '' || $locationId <= 0) {
            return false;
        }

        $baseUnit = $this->getBaseUnitContext($productId);
        if (!$baseUnit) {
            return false;
        }

        if ($unitId <= 0) {
            $unitId = $baseUnit['unit_id'];
        }

        if ($clearExisting) {
            $this->clearProductionOutput($productionId);
        }

        if ($quantity <= 0) {
            return true;
        }

        $quantityBase = $this->convertToBaseQuantity($productId, $unitId, $quantity);
        $lossBase     = $this->convertToBaseQuantity($productId, $unitId, ($payload['mortality_qty'] ?? 0) + ($payload['damaged_qty'] ?? 0));
        $extrasBase   = $this->convertToBaseQuantity($productId, $unitId, $payload['extras_qty'] ?? 0);

        $lotId = $this->ensureLot([
            'lot_code'              => $this->generateLotCode('PRD'),
            'product_id'            => $productId,
            'base_unit_id'          => $baseUnit['unit_id'],
            'location_id'           => $locationId,
            'initial_quantity'      => $quantityBase,
            'source_type'           => 'production',
            'source_reference'      => $productionId,
            'source_reference_line' => null,
            'production_date'       => $this->safeDate($payload['production_date'] ?? null) ?: date('Y-m-d'),
        ]);

        if (!$lotId) {
            return false;
        }

        $outputData = [
            'production_id' => $productionId,
            'product_id'    => $productId,
            'unit_id'       => $unitId,
            'location_id'   => $locationId,
            'lot_id'        => $lotId,
            'quantity'      => $quantityBase,
            'loss_quantity' => $lossBase,
            'cost_per_unit' => 0,
            'cost_total'    => 0,
            'created_by'    => $payload['created_by'] ?? null,
        ];

        $this->ci->db->insert('production_output_items', $outputData);
        $outputItemId = (int) $this->ci->db->insert_id();

        $reasonOutputId = $this->getReasonId('PRODUCTION_OUTPUT');
        if (!$reasonOutputId) {
            return false;
        }

        $movementInId = $this->createMovement([
            'movement_date'    => $this->safeDate($payload['production_date'] ?? null) ?: date('Y-m-d'),
            'product_id'       => $productId,
            'lot_id'           => $lotId,
            'location_id'      => $locationId,
            'unit_id'          => $baseUnit['unit_id'],
            'quantity_in'      => $quantityBase + $extrasBase,
            'quantity_out'     => 0,
            'cost_per_unit'    => 0,
            'cost_total'       => 0,
            'reason_id'        => $reasonOutputId,
            'reference_type'   => 'production',
            'reference_id'     => $productionId,
            'reference_line_id'=> $outputItemId,
            'narration'        => sprintf('Production output %s', $productionId),
            'created_by'       => $payload['created_by'] ?? null,
        ]);

        if (!$movementInId) {
            return false;
        }

        if ($lossBase > 0) {
            $reasonLossId = $this->getReasonId('PRODUCTION_LOSS');
            if (!$reasonLossId) {
                return false;
            }
            $this->createMovement([
                'movement_date'    => $this->safeDate($payload['production_date'] ?? null) ?: date('Y-m-d'),
                'product_id'       => $productId,
                'lot_id'           => $lotId,
                'location_id'      => $locationId,
                'unit_id'          => $baseUnit['unit_id'],
                'quantity_in'      => 0,
                'quantity_out'     => $lossBase,
                'cost_per_unit'    => 0,
                'cost_total'       => 0,
                'reason_id'        => $reasonLossId,
                'reference_type'   => 'production',
                'reference_id'     => $productionId,
                'reference_line_id'=> $outputItemId,
                'narration'        => sprintf('Production loss %s', $productionId),
                'created_by'       => $payload['created_by'] ?? null,
            ]);
        }

        return [
            'output_item_id' => $outputItemId,
            'movement_id'    => (int) $movementInId,
            'lot_id'         => $lotId,
        ];
    }

    public function recordTransfer(array $payload)
    {
        $sourceLotId    = (int) ($payload['source_lot_id'] ?? 0);
        $quantity       = (float) ($payload['quantity'] ?? 0);
        $unitId         = (int) ($payload['unit_id'] ?? 0);
        $toLocationId   = (int) ($payload['to_location_id'] ?? 0);
        $fromLocationId = (int) ($payload['from_location_id'] ?? 0);
        $transferDate   = $this->safeDate($payload['transfer_date'] ?? null) ?: date('Y-m-d');
        $createdBy      = $payload['created_by'] ?? null;
        $reference      = $payload['transfer_code'] ?? $this->generateTransferReference();
        $narration      = $payload['narration'] ?? null;

        if ($sourceLotId <= 0 || $quantity <= 0 || $toLocationId <= 0) {
            return false;
        }

        $sourceLot = $this->getLotRow($sourceLotId);
        if (!$sourceLot) {
            return false;
        }

        $productId  = $sourceLot['product_id'];
        $baseUnitId = (int) $sourceLot['base_unit_id'];

        if ($unitId <= 0) {
            $unitId = $baseUnitId;
        }
        if ($fromLocationId <= 0) {
            $fromLocationId = (int) $sourceLot['location_id'];
        }

        $quantityBase  = $this->convertToBaseQuantity($productId, $unitId, $quantity);
        $availableBase = $this->getLotAvailableQuantity($sourceLotId);
        if ($quantityBase <= 0 || $quantityBase - $availableBase > 1e-6) {
            return false;
        }

        $destinationLotId = (int) ($payload['destination_lot_id'] ?? 0);
        if ($destinationLotId > 0) {
            $destinationLot = $this->getLotRow($destinationLotId);
            if (
                !$destinationLot ||
                (string) $destinationLot['product_id'] !== (string) $productId ||
                (int) $destinationLot['location_id'] !== $toLocationId
            ) {
                return false;
            }
        } else {
            $destinationLotId = $this->ensureLot([
                'lot_code'              => $this->generateLotCode('TRF'),
                'product_id'            => $productId,
                'base_unit_id'          => $baseUnitId,
                'location_id'           => $toLocationId,
                'source_type'           => 'transfer',
                'source_reference'      => $sourceLotId,
                'source_reference_line' => null,
                'production_date'       => $sourceLot['production_date'] ?? null,
                'expiry_date'           => $sourceLot['expiry_date'] ?? null,
                'initial_quantity'      => $quantityBase,
                'notes'                 => $payload['notes'] ?? null,
            ]);
            if (!$destinationLotId) {
                return false;
            }
        }

        $reasonOutId = $this->ensureReason('TRANSFER_OUT', 'Transfer out', 'out', 'Stock moved to another location', 1);
        $reasonInId  = $this->ensureReason('TRANSFER_IN', 'Transfer in', 'in', 'Stock received from another location', 1);
        if (!$reasonOutId || !$reasonInId) {
            return false;
        }

        $lotSummary   = $this->getLotAggregateData($sourceLotId);
        $availableVal = $lotSummary['available_qty'] ?: 0.0;
        $valuation    = $lotSummary['valuation'] ?: 0.0;
        $averageCost  = ($availableVal > 0) ? ($valuation / $availableVal) : 0.0;
        $costPerUnit  = isset($payload['cost_per_unit']) ? (float) $payload['cost_per_unit'] : $averageCost;
        $costTotal    = $costPerUnit * $quantityBase;

        $this->ci->db->trans_begin();

        $movementOutId = $this->createMovement([
            'movement_date'     => $transferDate,
            'product_id'        => $productId,
            'lot_id'            => $sourceLotId,
            'location_id'       => $fromLocationId,
            'unit_id'           => $baseUnitId,
            'quantity_in'       => 0,
            'quantity_out'      => $quantityBase,
            'cost_per_unit'     => $costPerUnit,
            'cost_total'        => $costTotal,
            'reason_id'         => $reasonOutId,
            'reference_type'    => 'transfer',
            'reference_id'      => $reference,
            'reference_line_id' => $destinationLotId,
            'narration'         => $narration ?: sprintf('Transfer to location %s', $toLocationId),
            'created_by'        => $createdBy,
        ]);

        if (!$movementOutId) {
            $this->ci->db->trans_rollback();
            return false;
        }

        $movementInId = $this->createMovement([
            'movement_date'     => $transferDate,
            'product_id'        => $productId,
            'lot_id'            => $destinationLotId,
            'location_id'       => $toLocationId,
            'unit_id'           => $baseUnitId,
            'quantity_in'       => $quantityBase,
            'quantity_out'      => 0,
            'cost_per_unit'     => $costPerUnit,
            'cost_total'        => $costTotal,
            'reason_id'         => $reasonInId,
            'reference_type'    => 'transfer',
            'reference_id'      => $reference,
            'reference_line_id' => $sourceLotId,
            'narration'         => $narration ?: sprintf('Transfer from location %s', $fromLocationId),
            'created_by'        => $createdBy,
        ]);

        if (!$movementInId || $this->ci->db->trans_status() === false) {
            $this->ci->db->trans_rollback();
            return false;
        }

        $this->ci->db->trans_commit();

        $remaining = $this->getLotAvailableQuantity($sourceLotId);
        if ($remaining <= 0) {
            $this->ci->db->where('id', $sourceLotId)->update('stock_lots', ['status' => 'closed', 'location_id' => $toLocationId]);
        }

        if ($this->auditTrail) {
            $metadata = [
                'quantity_requested' => (float) ($payload['quantity'] ?? $quantity),
                'quantity_base'      => $quantityBase,
                'movement_out_id'    => (int) $movementOutId,
                'movement_in_id'     => (int) $movementInId,
                'destination_lot_id' => (int) $destinationLotId,
                'from_location_id'   => $fromLocationId,
                'to_location_id'     => $toLocationId,
                'narration'          => $narration,
            ];
            $notification = [
                'notification_type' => 'TRANSFER',
                'message'           => sprintf(
                    'Transfer %s moved %.2f units of %s to location %s',
                    $reference,
                    $quantity,
                    $productId,
                    $toLocationId
                ),
                'severity'          => 'info',
            ];
            $this->auditTrail->recordEventWithNotification('TRANSFER', [
                'reference_type' => 'transfer',
                'reference_id'   => $reference,
                'product_id'     => $productId,
                'lot_id'         => $sourceLotId,
                'location_id'    => $fromLocationId,
                'quantity'       => $quantityBase,
                'unit_id'        => $baseUnitId,
                'reason_code'    => 'TRANSFER_OUT',
                'created_by'     => $createdBy,
                'metadata'       => $metadata,
            ], $notification);
        }

        return [
            'transfer_code'      => $reference,
            'movement_out_id'    => (int) $movementOutId,
            'movement_in_id'     => (int) $movementInId,
            'destination_lot_id' => (int) $destinationLotId,
        ];
    }

    public function recordConsumption(array $payload)
    {
        $quantity   = (float) ($payload['quantity'] ?? 0);
        $unitId     = (int) ($payload['unit_id'] ?? 0);
        $reasonCode = strtoupper(trim($payload['reason_code'] ?? 'CONSUMPTION'));
        $createdBy  = $payload['created_by'] ?? null;

        if ($quantity <= 0) {
            return false;
        }

        $reasonId = $this->ensureReason(
            $reasonCode,
            ucwords(strtolower(str_replace('_', ' ', $reasonCode))),
            'out',
            'Inventory consumption',
            0
        );
        if (!$reasonId) {
            return false;
        }

        $options = [
            'reason_id'         => $reasonId,
            'reason_code'       => $reasonCode,
            'movement_date'     => $this->safeDate($payload['movement_date'] ?? null) ?: date('Y-m-d'),
            'reference_type'    => $payload['reference_type'] ?? 'consumption',
            'reference_id'      => $payload['reference_id'] ?? null,
            'reference_line_id' => $payload['reference_line_id'] ?? null,
            'narration'         => $payload['narration'] ?? 'Inventory consumption',
            'created_by'        => $createdBy,
            'allow_negative'    => !empty($payload['allow_negative']),
            'cost_per_unit'     => isset($payload['cost_per_unit']) ? (float) $payload['cost_per_unit'] : null,
        ];

        $lotId = (int) ($payload['lot_id'] ?? 0);
        if ($lotId > 0) {
            $lot = $this->getLotRow($lotId);
            if (!$lot) {
                return false;
            }
            if ($unitId <= 0) {
                $unitId = (int) $lot['base_unit_id'];
            }
            $quantityBase = $this->convertToBaseQuantity($lot['product_id'], $unitId, $quantity);
            if ($quantityBase <= 0) {
                return false;
            }
            $movementId = $this->consumeFromLot($lotId, $quantityBase, $options);
            if ($movementId === false) {
                return false;
            }
            $this->auditConsumptionEvent(
                $reasonCode,
                $options,
                [
                    [
                        'lot_id'       => $lotId,
                        'movement_id'  => $movementId,
                        'quantity_out' => $quantityBase,
                    ],
                ],
                $lot['product_id'],
                (int) $lot['location_id'],
                $quantityBase,
                (int) $lot['base_unit_id']
            );
            return $movementId;
        }

        $productId  = isset($payload['product_id']) ? trim($payload['product_id']) : '';
        $locationId = (int) ($payload['location_id'] ?? 0);
        if ($productId === '' || $locationId <= 0) {
            return false;
        }

        if ($unitId <= 0) {
            $base = $this->getBaseUnitContext($productId);
            if (!$base) {
                return false;
            }
            $unitId = (int) $base['unit_id'];
        }

        $quantityBase = $this->convertToBaseQuantity($productId, $unitId, $quantity);
        if ($quantityBase <= 0) {
            return false;
        }

        $lots = $this->getAvailableLotsForProduct($productId, $locationId);

        $totalAvailable = 0.0;
        foreach ($lots as $lotRow) {
            $totalAvailable += (float) $lotRow['available_qty'];
        }

        if (!$options['allow_negative'] && ($quantityBase - $totalAvailable) > 1e-6) {
            return false;
        }

        $options['product_id']  = $productId;
        $options['location_id'] = $locationId;

        $movements = [];
        $remaining = $quantityBase;

        foreach ($lots as $lotRow) {
            if ($remaining <= 0) {
                break;
            }
            $lotId = (int) $lotRow['id'];
            $take  = min($remaining, (float) $lotRow['available_qty']);
            if ($take <= 0) {
                continue;
            }

            $movementId = $this->consumeFromLot($lotId, $take, $options);
            if ($movementId === false) {
                return false;
            }

            $movements[] = [
                'lot_id'       => $lotId,
                'movement_id'  => $movementId,
                'quantity_out' => $take,
            ];
            $remaining -= $take;
        }

        if ($remaining > 0) {
            if (empty($lots)) {
                $fallbackLotId = $this->ensureAdjustmentLot($productId, $locationId);
                if (!$fallbackLotId) {
                    return false;
                }
            } else {
                $last = end($lots);
                $fallbackLotId = (int) $last['id'];
            }

            $overrideOptions = $options;
            $overrideOptions['allow_negative'] = true;
            $overrideOptions['narration'] = $this->decorateNarration($options['narration'], true);

            $movementId = $this->consumeFromLot($fallbackLotId, $remaining, $overrideOptions);
            if ($movementId === false) {
                return false;
            }

            $movements[] = [
                'lot_id'       => $fallbackLotId,
                'movement_id'  => $movementId,
                'quantity_out' => $remaining,
            ];
            $remaining = 0.0;
        }

        $this->auditConsumptionEvent(
            $reasonCode,
            $options,
            $movements,
            $productId,
            $locationId,
            $quantityBase,
            $unitId
        );

        return [
            'product_id'      => $productId,
            'location_id'     => $locationId,
            'movements'       => $movements,
            'quantity_base'   => $quantityBase,
        ];
    }

    public function recordWaste(array $payload)
    {
        $payload['reason_code']    = $payload['reason_code'] ?? 'WASTE';
        $payload['reference_type'] = $payload['reference_type'] ?? 'waste';
        $payload['narration']      = $payload['narration'] ?? 'Waste / spoilage';
        return $this->recordConsumption($payload);
    }

    /**
     * Retrieve open lots with available quantity for a product at a location.
     *
     * @return array<int,array{
     *   id:int, lot_code:string, product_id:string, base_unit_id:int,
     *   location_id:int, available_qty:float, production_date:?string, created_at:?string
     * }>
     */
    protected function getAvailableLotsForProduct($productId, $locationId)
    {
        $sub = $this->ci->db->select('lot_id, SUM(quantity_in - quantity_out) AS qty')
            ->from('stock_movements')
            ->group_by('lot_id')
            ->get_compiled_select();

        return $this->ci->db->select([
                'l.id',
                'l.lot_code',
                'l.product_id',
                'l.base_unit_id',
                'l.location_id',
                'COALESCE(m.qty, 0) AS available_qty',
                'l.production_date',
                'l.created_at',
            ])
            ->from('stock_lots l')
            ->join("($sub) m", 'm.lot_id = l.id', 'left')
            ->where('l.product_id', $productId)
            ->where('l.location_id', $locationId)
            ->where('l.status', 'open')
            ->where('COALESCE(m.qty, 0) >', 0)
            ->order_by('l.production_date', 'asc')
            ->order_by('l.created_at', 'asc')
            ->order_by('l.id', 'asc')
            ->get()
            ->result_array();
    }

    /**
     * Ensure an adjustment lot exists for product/location to host overrides.
     */
    protected function ensureAdjustmentLot($productId, $locationId)
    {
        $base = $this->getBaseUnitContext($productId);
        if (!$base) {
            return null;
        }

        return $this->ensureLot([
            'lot_code'         => $this->generateLotCode('ADJ'),
            'product_id'       => $productId,
            'base_unit_id'     => $base['unit_id'],
            'location_id'      => $locationId,
            'source_type'      => 'adjustment',
            'source_reference' => null,
            'initial_quantity' => 0,
            'notes'            => 'Auto-generated adjustment lot',
        ]);
    }

    /**
     * Execute a consumption movement against a specific lot (quantity in base units).
     */
    protected function consumeFromLot($lotId, $quantityBase, array $options)
    {
        $lot = $this->getLotRow($lotId);
        if (!$lot || $quantityBase <= 0) {
            return false;
        }

        $allowNegative = !empty($options['allow_negative']);
        $reasonId      = $options['reason_id'];
        $reasonCode    = $options['reason_code'] ?? '';

        $availableBase = $this->getLotAvailableQuantity($lotId);
        $insufficient  = ($quantityBase - $availableBase) > 1e-6;
        if (!$allowNegative && $insufficient) {
            return false;
        }

        $lotSummary   = $this->getLotAggregateData($lotId);
        $availableVal = $lotSummary['available_qty'] ?: 0.0;
        $valuation    = $lotSummary['valuation'] ?: 0.0;
        $averageCost  = ($availableVal > 0) ? ($valuation / $availableVal) : 0.0;
        $costPerUnit  = $options['cost_per_unit'] ?? $averageCost;
        $costTotal    = $costPerUnit * $quantityBase;

        $narration = $options['narration'] ?? 'Inventory consumption';
        if ($insufficient && $allowNegative) {
            $narration = $this->decorateNarration($narration, true);
        }

        $movementId = $this->createMovement([
            'movement_date'     => $options['movement_date'],
            'product_id'        => $lot['product_id'],
            'lot_id'            => $lotId,
            'location_id'       => (int) $lot['location_id'],
            'unit_id'           => (int) $lot['base_unit_id'],
            'quantity_in'       => 0,
            'quantity_out'      => $quantityBase,
            'cost_per_unit'     => $costPerUnit,
            'cost_total'        => $costTotal,
            'reason_id'         => $reasonId,
            'reference_type'    => $options['reference_type'] ?? 'consumption',
            'reference_id'      => $options['reference_id'] ?? null,
            'reference_line_id' => $options['reference_line_id'] ?? null,
            'narration'         => $narration,
            'created_by'        => $options['created_by'] ?? null,
        ]);

        if (!$movementId) {
            return false;
        }

        if (!$allowNegative) {
            $remaining = $this->getLotAvailableQuantity($lotId);
            if ($remaining <= 0) {
                $this->ci->db->where('id', $lotId)->update('stock_lots', ['status' => 'closed']);
            }
        }

        return (int) $movementId;
    }

    /**
     * Remove movements tied to a specific reference.
     */
    public function clearMovementsByReference($referenceType, $referenceId)
    {
        if ($referenceType === '' || $referenceId === '' || $referenceId === null) {
            return;
        }

        $rows = $this->ci->db->select('id, lot_id')
            ->from('stock_movements')
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->get()
            ->result_array();

        if (empty($rows)) {
            return;
        }

        $movementIds = array_column($rows, 'id');
        $this->ci->db->where_in('id', $movementIds)->delete('stock_movements');
    }

    /**
     * Remove purchase movements/lots associated with a purchase reference.
     */
    public function clearPurchaseReceipt($purchaseReference)
    {
        if (empty($purchaseReference)) {
            return;
        }

        $rows = $this->ci->db->select('id, lot_id')
            ->from('stock_movements')
            ->where('reference_type', 'purchase')
            ->where('reference_id', $purchaseReference)
            ->get()
            ->result_array();

        if (empty($rows)) {
            return;
        }

        $movementIds = array_column($rows, 'id');
        $lotIds = array_unique(array_filter(array_column($rows, 'lot_id')));

        $this->ci->db->where_in('id', $movementIds)->delete('stock_movements');

        foreach ($lotIds as $lotId) {
            $remaining = $this->ci->db->from('stock_movements')
                ->where('lot_id', $lotId)
                ->count_all_results();
            if ($remaining === 0) {
                $this->ci->db->where('id', $lotId)->delete('stock_lots');
            }
        }
    }

    /**
     * Remove existing production output items + related movements.
     */
    public function clearProductionOutput($productionId)
    {
        $productionId = (int) $productionId;
        if ($productionId <= 0) {
            return;
        }

        $movements = $this->ci->db->select('id')
            ->from('stock_movements')
            ->where('reference_type', 'production')
            ->where('reference_id', $productionId)
            ->get()
            ->result_array();
        if (!empty($movements)) {
            $movementIds = array_column($movements, 'id');
            $this->ci->db->where_in('id', $movementIds)->delete('stock_movements');
        }

        $this->ci->db->where('production_id', $productionId)->delete('production_output_items');
    }

    /**
     * Ensure lot exists and return id.
     */
    protected function ensureLot(array $data)
    {
        $productId   = $data['product_id'];
        $lotCode     = $data['lot_code'] ?? null;

        if ($lotCode) {
            $existing = $this->ci->db->select('id')
                ->from('stock_lots')
                ->where('lot_code', $lotCode)
                ->where('product_id', $productId)
                ->limit(1)
                ->get()
                ->row();
            if ($existing) {
                return (int) $existing->id;
            }
        }

        $insert = [
            'lot_code'              => $lotCode ?: $this->generateLotCode(),
            'product_id'            => $productId,
            'base_unit_id'          => $data['base_unit_id'],
            'location_id'           => $data['location_id'],
            'source_type'           => $data['source_type'] ?? null,
            'source_reference'      => $data['source_reference'] ?? null,
            'source_reference_line' => $data['source_reference_line'] ?? null,
            'initial_quantity'      => $data['initial_quantity'] ?? 0,
            'status'                => 'open',
            'notes'                 => $data['notes'] ?? null,
        ];

        if (!empty($data['production_date'])) {
            $insert['production_date'] = $data['production_date'];
        }
        if (!empty($data['expiry_date'])) {
            $insert['expiry_date'] = $data['expiry_date'];
        }

        $this->ci->db->insert('stock_lots', $insert);
        if ($this->ci->db->error()['code']) {
            return false;
        }
        return (int) $this->ci->db->insert_id();
    }

    /**
     * Insert a stock movement record, returning movement id.
     */
    protected function createMovement(array $data)
    {
        $insert = [
            'movement_date'    => $data['movement_date'] ?? date('Y-m-d'),
            'product_id'       => $data['product_id'],
            'lot_id'           => $data['lot_id'],
            'location_id'      => $data['location_id'],
            'unit_id'          => $data['unit_id'],
            'quantity_in'      => $data['quantity_in'] ?? 0,
            'quantity_out'     => $data['quantity_out'] ?? 0,
            'cost_per_unit'    => $data['cost_per_unit'] ?? 0,
            'cost_total'       => $data['cost_total'] ?? 0,
            'reason_id'        => $data['reason_id'],
            'reference_type'   => $data['reference_type'] ?? null,
            'reference_id'     => $data['reference_id'] ?? null,
            'reference_line_id'=> $data['reference_line_id'] ?? null,
            'narration'        => $data['narration'] ?? null,
            'created_by'       => $data['created_by'] ?? null,
        ];

        $this->ci->db->insert('stock_movements', $insert);
        if ($this->ci->db->error()['code']) {
            return false;
        }
        return (int) $this->ci->db->insert_id();
    }

    /**
     * Convert quantity to base unit using product_units conversion_factor.
     */
    protected function convertToBaseQuantity($productId, $unitId, $quantity)
    {
        $unitRow = $this->getProductUnitRow($productId, $unitId);
        if (!$unitRow) {
            return (float) $quantity;
        }
        return (float) $quantity * (float) $unitRow['conversion_factor'];
    }

    /**
     * Load and cache product_units rows for a product.
     */
    protected function loadProductUnitCache($productId)
    {
        if (isset($this->productUnitCache[$productId])) {
            return $this->productUnitCache[$productId];
        }

        $rows = $this->ci->db->select('product_id, unit_id, conversion_factor, is_base, is_default_purchase')
            ->from('product_units')
            ->where('product_id', $productId)
            ->get()
            ->result_array();

        if (empty($rows)) {
            $product = $this->ci->db->select('unit')
                ->from('product_information')
                ->where('product_id', $productId)
                ->get()
                ->row();
            if ($product && !empty($product->unit)) {
                $unitRow = $this->ci->db->select('unit_id')
                    ->from('units')
                    ->where('unit_name', $product->unit)
                    ->get()
                    ->row();
                if ($unitRow) {
                    $rows[] = [
                        'product_id'         => $productId,
                        'unit_id'            => (int) $unitRow->unit_id,
                        'conversion_factor'  => 1.000000,
                        'is_base'            => 1,
                        'is_default_purchase'=> 1,
                    ];
                }
            }
        }

        $this->productUnitCache[$productId] = $rows;
        return $rows;
    }

    /**
     * Return base unit context for a product.
     *
     * @return array|null { unit_id:int, conversion_factor:float }
     */
    public function getBaseUnitContext($productId)
    {
        $rows = $this->loadProductUnitCache($productId);
        foreach ($rows as $row) {
            if (!empty($row['is_base'])) {
                return [
                    'unit_id'           => (int) $row['unit_id'],
                    'conversion_factor' => (float) $row['conversion_factor'],
                ];
            }
        }
        if (!empty($rows)) {
            $row = reset($rows);
            return [
                'unit_id'           => (int) $row['unit_id'],
                'conversion_factor' => (float) $row['conversion_factor'],
            ];
        }
        return null;
    }

    /**
     * Return the base unit id for a product (or null if missing).
     */
    public function getBaseUnitId($productId)
    {
        $context = $this->getBaseUnitContext($productId);
        return $context ? (int) $context['unit_id'] : null;
    }

    /**
     * Return the preferred purchase unit id (default purchase -> base fallback).
     */
    public function getPreferredPurchaseUnitId($productId)
    {
        $rows = $this->loadProductUnitCache($productId);
        foreach ($rows as $row) {
            if (!empty($row['is_default_purchase'])) {
                return (int) $row['unit_id'];
            }
        }
        $base = $this->getBaseUnitContext($productId);
        return $base ? (int) $base['unit_id'] : null;
    }

    /**
     * Convert a quantity from the specified unit to the product base unit.
     */
    public function convertToBase($productId, $unitId, $quantity)
    {
        return $this->convertToBaseQuantity($productId, $unitId, $quantity);
    }

    /**
     * Convert a quantity expressed in the product base unit back to the specified unit.
     */
    public function convertFromBaseQuantity($productId, $unitId, $quantity)
    {
        $quantity = (float) $quantity;
        if ($quantity === 0.0) {
            return 0.0;
        }

        $unitId = (int) $unitId;
        $base = $this->getBaseUnitContext($productId);
        if (!$base) {
            return $quantity;
        }

        if ($unitId <= 0 || (int) $base['unit_id'] === $unitId) {
            return $quantity;
        }

        $row = $this->getProductUnitRow($productId, $unitId);
        if (!$row || empty($row['conversion_factor'])) {
            return $quantity;
        }

        $factor = (float) $row['conversion_factor'];
        if ($factor === 0.0) {
            return $quantity;
        }

        return $quantity / $factor;
    }

    /**
     * Alias for convertFromBaseQuantity.
     */
    public function convertFromBase($productId, $unitId, $quantity)
    {
        return $this->convertFromBaseQuantity($productId, $unitId, $quantity);
    }

    /**
     * Fetch a lot record by id.
     *
     * @return array|null
     */
    protected function getLotRow($lotId)
    {
        return $this->ci->db->select('*')
            ->from('stock_lots')
            ->where('id', (int) $lotId)
            ->limit(1)
            ->get()
            ->row_array();
    }

    /**
     * Summarise movement quantities and cost for a lot.
     *
     * @return array{quantity_in:float,quantity_out:float,cost_in:float,cost_out:float,available_qty:float,valuation:float}
     */
    protected function getLotAggregateData($lotId)
    {
        $row = $this->ci->db->select([
                'COALESCE(SUM(CASE WHEN quantity_in  > 0 THEN quantity_in  ELSE 0 END),0) AS total_in',
                'COALESCE(SUM(CASE WHEN quantity_out > 0 THEN quantity_out ELSE 0 END),0) AS total_out',
                'COALESCE(SUM(CASE WHEN quantity_in  > 0 THEN cost_total ELSE 0 END),0) AS cost_in',
                'COALESCE(SUM(CASE WHEN quantity_out > 0 THEN cost_total ELSE 0 END),0) AS cost_out',
            ])
            ->from('stock_movements')
            ->where('lot_id', (int) $lotId)
            ->get()
            ->row();

        $totalIn = $row ? (float) $row->total_in : 0.0;
        $totalOut = $row ? (float) $row->total_out : 0.0;
        $costIn = $row ? (float) $row->cost_in : 0.0;
        $costOut = $row ? (float) $row->cost_out : 0.0;

        return [
            'quantity_in'   => $totalIn,
            'quantity_out'  => $totalOut,
            'cost_in'       => $costIn,
            'cost_out'      => $costOut,
            'available_qty' => $totalIn - $totalOut,
            'valuation'     => $costIn - $costOut,
        ];
    }

    /**
     * Return available quantity for the lot (in base units).
     */
    protected function getLotAvailableQuantity($lotId)
    {
        $summary = $this->getLotAggregateData($lotId);
        return $summary['available_qty'];
    }

    /**
     * Ensure a movement reason exists, inserting it if necessary.
     *
     * @return int|null
     */
    protected function ensureReason($code, $name, $direction, $description = '', $isSystem = 0)
    {
        $existing = $this->getReasonId($code);
        if ($existing) {
            return $existing;
        }

        $insert = [
            'reason_code'          => strtoupper(trim($code)),
            'reason_name'          => $name,
            'movement_direction'   => $direction,
            'description'          => $description,
            'is_system'            => (int) $isSystem,
            'is_active'            => 1,
            'created_at'           => date('Y-m-d H:i:s'),
        ];

        $this->ci->db->insert('stock_movement_reasons', $insert);
        if ($this->ci->db->error()['code']) {
            return $this->getReasonId($code);
        }

        $id = (int) $this->ci->db->insert_id();
        $this->reasonCache[strtoupper(trim($code))] = $id;
        return $id;
    }

    /**
     * Return a non-base unit row (for conversion lookup).
     *
     * @return array|null
     */
    protected function getProductUnitRow($productId, $unitId)
    {
        $rows = $this->loadProductUnitCache($productId);
        foreach ($rows as $row) {
            if ((int) $row['unit_id'] === (int) $unitId) {
                return $row;
            }
        }
        $base = $this->getBaseUnitContext($productId);
        if ($base) {
            return [
                'unit_id'           => $base['unit_id'],
                'conversion_factor' => $base['conversion_factor'],
            ];
        }
        return null;
    }

    /**
     * Retrieve reason id by code (cached).
     */
    protected function getReasonId($code)
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return null;
        }
        if (isset($this->reasonCache[$code])) {
            return $this->reasonCache[$code];
        }
        $row = $this->ci->db->select('id')
            ->from('stock_movement_reasons')
            ->where('reason_code', $code)
            ->limit(1)
            ->get()
            ->row();
        if (!$row) {
            return null;
        }
        $this->reasonCache[$code] = (int) $row->id;
        return $this->reasonCache[$code];
    }

    /**
     * Determine if a location exists.
     */
    protected function locationExists($locationId)
    {
        $row = $this->ci->db->select('id')
            ->from('stock_locations')
            ->where('id', (int) $locationId)
            ->where('is_active', 1)
            ->limit(1)
            ->get()
            ->row();
        return (bool) $row;
    }

    /**
     * Retrieve the fallback/default location id (MAIN_STORE or first active).
     */
    public function getDefaultLocationId()
    {
        if ($this->defaultLocationId !== null) {
            return $this->defaultLocationId;
        }

        $row = $this->ci->db->select('id')
            ->from('stock_locations')
            ->where('location_code', 'MAIN_STORE')
            ->limit(1)
            ->get()
            ->row();
        if ($row) {
            $this->defaultLocationId = (int) $row->id;
            return $this->defaultLocationId;
        }

        $row = $this->ci->db->select('id')
            ->from('stock_locations')
            ->where('is_active', 1)
            ->order_by('id', 'asc')
            ->limit(1)
            ->get()
            ->row();
        if ($row) {
            $this->defaultLocationId = (int) $row->id;
            return $this->defaultLocationId;
        }
        return null;
    }

    /**
     * Convert arbitrary date string to YYYY-MM-DD or null.
     */
    protected function safeDate($value)
    {
        if (empty($value)) {
            return null;
        }
        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return null;
        }
        return date('Y-m-d', $timestamp);
    }

    /**
     * Generate unique lot code with prefix.
     */
    protected function generateLotCode($prefix = 'LOT')
    {
        return sprintf('%s-%s-%s', strtoupper($prefix), date('YmdHis'), substr(md5(uniqid((string) rand(), true)), 0, 6));
    }

    /**
     * Generate a unique reference for a transfer transaction.
     */
    protected function generateTransferReference()
    {
        return sprintf('TRF-%s-%s', date('YmdHis'), substr(md5(uniqid((string) rand(), true)), 0, 4));
    }

    /**
     * Record consumption or waste audit and notification entries.
     *
     * @param string $reasonCode
     * @param array<string,mixed> $options
     * @param array<int,array<string,mixed>> $movements
     * @param string $productId
     * @param int $locationId
     * @param float $quantityBase
     * @param int $unitId
     */
    protected function auditConsumptionEvent($reasonCode, array $options, array $movements, $productId, $locationId, $quantityBase, $unitId)
    {
        if (!$this->auditTrail) {
            return;
        }

        $reasonCode = strtoupper(trim($reasonCode));
        $eventType = (stripos($reasonCode, 'WASTE') !== false || stripos($reasonCode, 'SCRAP') !== false)
            ? 'WASTE'
            : 'CONSUMPTION';

        $baseContext = $this->getBaseUnitContext($productId);
        $unitForEvent = $baseContext ? (int) $baseContext['unit_id'] : (int) $unitId;

        $metadata = [
            'movements'      => $movements,
            'allow_negative' => !empty($options['allow_negative']),
            'narration'      => $options['narration'] ?? null,
            'reason_code'    => $reasonCode,
        ];
        if (isset($options['reference_line_id'])) {
            $metadata['reference_line_id'] = $options['reference_line_id'];
        }
        if (isset($options['product_id']) && $options['product_id'] !== $productId) {
            $metadata['requested_product_id'] = $options['product_id'];
        }
        if (!empty($options['location_id']) && (int) $options['location_id'] !== (int) $locationId) {
            $metadata['requested_location_id'] = (int) $options['location_id'];
        }

        $severity = (!empty($options['allow_negative']) || stripos($reasonCode, 'WASTE') !== false) ? 'warning' : 'info';

        $message = sprintf(
            '%s recorded: %.2f units of %s at location %s',
            ucwords(strtolower($eventType)),
            $quantityBase,
            $productId,
            $locationId
        );
        if (!empty($options['reference_id'])) {
            $message .= sprintf(' (ref %s)', $options['reference_id']);
        }

        $this->auditTrail->recordEventWithNotification($eventType, [
            'reference_type' => $options['reference_type'] ?? 'consumption',
            'reference_id'   => $options['reference_id'] ?? null,
            'product_id'     => $productId,
            'location_id'    => $locationId,
            'quantity'       => $quantityBase,
            'unit_id'        => $unitForEvent,
            'reason_code'    => $reasonCode,
            'created_by'     => $options['created_by'] ?? null,
            'metadata'       => $metadata,
        ], [
            'notification_type' => $eventType,
            'message'           => $message,
            'severity'          => $severity,
        ]);
    }

    /**
     * Append override marker to narration if needed.
     */
    protected function decorateNarration($narration, $overrideApplied = false)
    {
        if (!$overrideApplied) {
            return $narration;
        }
        $text = $narration ?: 'Inventory consumption';
        return trim($text . ' [override]');
    }
}
