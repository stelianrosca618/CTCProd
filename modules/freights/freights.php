<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Freights
Description: Default module for defining Freights
Version: 1.0.0
Requires at least: 2.3.*
*/

define('FREIGHTS_MODULE_NAME', 'freights');

hooks()->add_action('admin_init', 'freights_module_init_menu_items');

/**
* Register activation module hook
*/
register_activation_hook(FREIGHTS_MODULE_NAME, 'freights_module_activation_hook');

function freights_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(FREIGHTS_MODULE_NAME, [FREIGHTS_MODULE_NAME]);

/**
* Init freights module menu items in setup in admin_init hook
* @return null
*/
function freights_module_init_menu_items()
{
    $CI = &get_instance();

    /**
    * If the logged in user is administrator, add custom menu in Setup
    */
    if (is_admin()) {
        $CI->app_menu->add_sidebar_menu_item('freights', [
            'name'     => _l('freights'),
            'href'     => admin_url('freights'),
            'icon'     => 'fa-solid fa-truck-fast',
            'position' => 46,
            'badge'    => [],
        ]);
    }
}