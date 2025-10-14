<?php
// module directory name
$HmvcConfig['livestock']['_title']       = 'Livestock Management';
$HmvcConfig['livestock']['_description'] = 'Module for managing sheds, livestock, productions, and feed usage.';
$HmvcConfig['livestock']['_database']    = true;
$HmvcConfig['livestock']['_tables']      = array(
    'sheds',
    'productions',
    'livestock_groups',
    'livestocks',
    'feeds',
    'feed_usages',
);
