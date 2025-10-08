<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('dd')) {
    /**
     * Dump the given variables and terminate the script.
     *
     * @param mixed ...$vars Values to inspect.
     */
    function dd(...$vars)
    {
        if (empty($vars)) {
            $vars = [null];
        }

        foreach ($vars as $var) {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        }

        exit(1);
    }
}
