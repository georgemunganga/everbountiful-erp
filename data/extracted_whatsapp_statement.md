# WhatsApp Data Extraction (Customer +260978821001)

Source: `WhatsApp Chat with +260 978821001.txt`
Period covered in entries below: 2025-10-20 to 2025-10-30

Assumptions
- Currency: ZMW (Kwacha). Where not stated, amounts assumed ZMW.
- “Pay Later” = credit sale (customer account). “Pay Now”/Cash Sale = immediate cash.
- “Home” trays and “under expenses” trays are treated as internal consumption (expense) at farm’s internal rate (please confirm unit rate).
- “Trays In” are treated as production inflow (stock collected by trays). “Trays Out” might be a sale or internal consumption; when unclear which, move it to Unclear Records (do not book as sale/expense).

Open Questions
1) Confirm currency (ZMW) and default unit price to value egg-tray expenses (“Home”, “under expenses”).
2) For “Airtel Money” (9/24), was any payment amount received? If yes, amount and payer? (currently filed under Unclear Records)
3) For “Bornface Nondo 200 trays @70”, was that an invoice (credit) or a delivery that day? Any payment received?
4) Confirm whether “Trays Out” should always be split into sales vs. internal usage vs. other.

## Productions / Stock Collections

| Date       | Eggs Picked | Trays | Trays Out | Trays In | Damages | Extras | Mortality | Feed Used (bags) | %   |
|------------|-------------:|------:|----------:|---------:|--------:|-------:|----------:|------------------:|-----|
| 20/10/2025 |        1735 |    57 |         0 |      303 |       3 |     22 |         0 |                 4 | 86.84 |
| 21/10/2025 |        1756 |    58 |       227 |      134 |       6 |     10 |         0 |                 4 | 87.89 |
| 22/10/2025 |        1720 |    57 |        10 |      181 |       5 |      5 |         1 |                 4 | 86.13 |
| 23/10/2025 |        1731 |    57 |           |      238 |       6 |     15 |         0 |                 4 | 86.68 |
| 24/10/2025 |        1683 |    56 |           |      294 |       3 |        |         0 |                 4 | 84.28 |
| 25/10/2025 |        1694 |    56 |        51 |      299 |         |     14 |         1 |                 4 | 84.87 |
| 26/10/2025 |        1712 |    57 |       134 |      222 |       2 |        |         0 |                 4 | 85.77 |
| 27/10/2025 |        1734 |    57 |           |      279 |       8 |     16 |         1 |                 4 | 86.92 |
| 28/10/2025 |        1734 |    57 |       102 |      234 |       4 |     14 |         0 |                 4 | 86.62 |
| 29/10/2025 |        1636 |    54 | 102 → 103 |      186 |       6 |     10 |         0 |                 4 | 82.01 |
| 30/10/2025 |        1634 |    54 |         1 |      239 |       5 |      9 |         1 |                 4 | 81.95 |

Notes
- 20/10: Shed 2 opened; day-old birds arriving soon (setup/stocking referenced earlier in chat).
- 26/10: Add 2 mortality for Shed 2 on 25/10.

## Sales (Deliveries and Cash)

| Date       | Customer           | Qty (trays) | Unit Price | Total  | Type     | Notes |
|------------|--------------------|------------:|-----------:|-------:|----------|-------|
| 21/10/2025 | Bornface Nondo     |         200 |        70  | 14,000 | Pay Later? | Instruction to add customer account; please confirm invoice vs. delivery |
| 26/10/2025 | Vinnid             |         100 |           |        | Pay Later | Delivery; requested invoice + statement |
| 25/10/2025 | Cash Sale          |           1 |        75  |     75 | Cash     | CS 1 @75 |
| 26/10/2025 | Cash Sale          |          10 |        70  |    700 | Cash     | CS 10 @70 |
| 26/10/2025 | Cash Sale          |          20 |        70  |  1,400 | Cash     | CS 20 @70 |
| 26/10/2025 | Cash Sale          |           2 |        75  |    150 | Cash     | CS 2 @75 |
| 28/10/2025 | Cash Sale (Walk-in)|           2 |        75  |    150 | Cash     | Daily sales addition |
| 29/10/2025 | Peter (Account)    |         100 |           |        | Pay Later | Delivered 100 trays to Peter |
 
| 30/10/2025 | Cash Sale          |           1 |        75  |     75 | Cash     | Today’s cash sale @75 |

 

## Payments (Customer Receipts)

| Date       | Payer   | Amount | Method       | Reference/Notes |
|------------|---------|-------:|--------------|-----------------|
| 23/10/2025 | Peter   |  5,000 | (unspecified)| “add a payment made today” |
| 26/10/2025 | Poshano |  5,000 | (unspecified)| “received 5000 from Poshano’s account” |
| 25/10/2025 | Peter   |    500 | (unspecified)| “Peter made a payment of 500 kwacha” |
| 28/10/2025 | Poshano |  1,000 | (unspecified)| Payment received 1000; same day delivered 100 trays @ 65 |

(Consider mapping payment methods to COA where possible; “Airtel Money” mentioned 9/24 but no amount provided.)

## Expenses (Farm + Internal Consumption)

| Date       | Item / Description                                  | Qty | Unit Price | Total   | Shed | Notes |
|------------|-------------------------------------------------------|----:|-----------:|--------:|------|-------|
| 12/10/2025 | HOME TRAY                                            |   1 |    (TBD)   |  (TBD)  |      | Internal use |
| 29/10/2025 | Eggs (under expenses)                                |   2 |    (TBD)   |  (TBD)  |      | Internal use |
| 29/10/2025 | Symbiotic 1kg                                        |   1 |     586.20|   586.20|  1   |        |
| 29/10/2025 | E-selen 1ltr                                         |   2 |     554.40| 1,108.80|  1   |        |
| 29/10/2025 | D3 calcium 1ltr                                      |   1 |     471.28|   471.28|  1   |        |
| 29/10/2025 | Fly catcher bait & bag                               |   8 |      42.55|   340.40|  1   | “each” indicated |
| 28/10/2025 | Vaccines: IB ND live; Poultry Tonic 5L               |     |           | 2,095.00|  1   | “160 & poultry tonic 5litre 2, 095” – please confirm the split |
| 28/10/2025 | Vaccines: Lasota & Gumboro                           |     |           |   153.00|  2   |        |

## Unclear Records (Not booked as sale/expense until clarified)

- 26/10/2025 SULA 1 TRAY (party not identified; exclude from sales/expenses)
- 29/10/2025 1 tray cash sale (price not specified; cannot value)
- 9/24/2025 “Airtel Money” mentioned (no amount; no payer) 

To Confirm
- Unit rate (ZMW/tray) to value “HOME” or “under expenses” trays (use e.g., 70 or 75 depending on day?).
- Any delivery costs (transport/fuel) to add?

---

If you confirm the open questions, I can normalize totals (fill TBD), and output a companion CSV for each section.
