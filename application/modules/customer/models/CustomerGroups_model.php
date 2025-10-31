<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Compatibility shim for case-sensitive hosts:
// Some code paths may attempt to load 'Customergroups_model' (lowercase g)
// while the canonical model is 'CustomerGroups_model'.
// This lightweight shim extends the canonical class so both names work.

// Ensure the canonical class is available
require_once __DIR__ . '/CustomerGroups_model.php';

class Customergroups_model extends CustomerGroups_model {}

