<?php

$addOnOf = 'worksuite-new';

return [
    'name' => 'Asset',
    'verification_required' => false,
    'envato_item_id' => '25798074',
    'parent_envato_id' => 20052522,
    'parent_min_version' => '5.2.3',
    'script_name' => $addOnOf.'-asset-module',
    'parent_product_name' => $addOnOf,
    'setting' => \Modules\Asset\Entities\AssetSetting::class,
];
