<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * InventoryAuditTrail
 *
 * Lightweight helper for recording inventory audit events and notifications.
 */
class InventoryAuditTrail
{
    /** @var CI_Controller */
    protected $ci;

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->database();
    }

    /**
     * Persist an inventory audit event.
     *
     * @param string $eventType
     * @param array<string,mixed> $payload
     * @return int|null
     */
    public function recordEvent($eventType, array $payload)
    {
        $eventType = strtoupper(trim((string) $eventType));
        if ($eventType === '') {
            return null;
        }

        $data = array(
            'event_type'     => $eventType,
            'reference_type' => $payload['reference_type'] ?? null,
            'reference_id'   => $payload['reference_id'] ?? null,
            'product_id'     => $payload['product_id'] ?? null,
            'lot_id'         => $this->nullableInt($payload['lot_id'] ?? null),
            'location_id'    => $this->nullableInt($payload['location_id'] ?? null),
            'quantity'       => isset($payload['quantity']) ? (float) $payload['quantity'] : null,
            'unit_id'        => $this->nullableInt($payload['unit_id'] ?? null),
            'reason_code'    => isset($payload['reason_code']) ? strtoupper(trim((string) $payload['reason_code'])) : null,
            'metadata'       => $this->encodeMetadata($payload['metadata'] ?? null),
            'created_by'     => $payload['created_by'] ?? null,
            'created_at'     => date('Y-m-d H:i:s'),
        );

        $this->ci->db->insert('inventory_audit_events', $data);
        $error = $this->ci->db->error();
        if (!empty($error['code'])) {
            log_message('error', 'Failed to write inventory audit event: ' . json_encode($error));
            return null;
        }

        return (int) $this->ci->db->insert_id();
    }

    /**
     * Persist a notification entry.
     *
     * @param array<string,mixed> $payload
     * @return int|null
     */
    public function notify(array $payload)
    {
        $message = isset($payload['message']) ? trim((string) $payload['message']) : '';
        if ($message === '') {
            return null;
        }

        $data = array(
            'audit_event_id'    => $this->nullableInt($payload['audit_event_id'] ?? null),
            'notification_type' => strtoupper(trim((string) ($payload['notification_type'] ?? 'GENERAL'))),
            'message'           => $message,
            'severity'          => strtolower(trim((string) ($payload['severity'] ?? 'info'))),
            'reference_type'    => $payload['reference_type'] ?? null,
            'reference_id'      => $payload['reference_id'] ?? null,
            'is_read'           => 0,
            'created_by'        => $payload['created_by'] ?? null,
            'created_at'        => date('Y-m-d H:i:s'),
        );

        $this->ci->db->insert('inventory_notifications', $data);
        $error = $this->ci->db->error();
        if (!empty($error['code'])) {
            log_message('error', 'Failed to write inventory notification: ' . json_encode($error));
            return null;
        }

        return (int) $this->ci->db->insert_id();
    }

    /**
     * Convenience helper to record an event and optional notification in one call.
     *
     * @param string $eventType
     * @param array<string,mixed> $eventPayload
     * @param array<string,mixed> $notificationPayload
     * @return int|null
     */
    public function recordEventWithNotification($eventType, array $eventPayload, array $notificationPayload = array())
    {
        $eventId = $this->recordEvent($eventType, $eventPayload);
        if (!$eventId) {
            return null;
        }

        if (!empty($notificationPayload)) {
            $notificationPayload['audit_event_id'] = $eventId;
            if (!isset($notificationPayload['reference_type'])) {
                $notificationPayload['reference_type'] = $eventPayload['reference_type'] ?? null;
            }
            if (!isset($notificationPayload['reference_id'])) {
                $notificationPayload['reference_id'] = $eventPayload['reference_id'] ?? null;
            }
            if (!isset($notificationPayload['created_by'])) {
                $notificationPayload['created_by'] = $eventPayload['created_by'] ?? null;
            }
            $this->notify($notificationPayload);
        }

        return $eventId;
    }

    /**
     * Convert value to nullable int.
     *
     * @param mixed $value
     * @return int|null
     */
    private function nullableInt($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }
        $intValue = (int) $value;
        return $intValue > 0 ? $intValue : null;
    }

    /**
     * Encode metadata payload to JSON.
     *
     * @param mixed $metadata
     * @return string|null
     */
    private function encodeMetadata($metadata)
    {
        if ($metadata === null) {
            return null;
        }
        if (is_string($metadata)) {
            return $metadata;
        }
        $encoded = json_encode($metadata);
        if ($encoded === false) {
            return null;
        }
        return $encoded;
    }
}

