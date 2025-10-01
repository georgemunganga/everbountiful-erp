<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$hook['post_controller_constructor'][] = array(
    'class'    => 'DemoRestriction',
    'function' => 'check_request_method',
    'filename' => 'DemoRestriction.php',
    'filepath' => 'hooks',
    'params'   => array()
);
