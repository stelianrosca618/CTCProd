<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Missiveapp iFrame Integrations
Description: Default module for defining missiveapp
Version: 1.0.0
Requires at least: 2.3.*
*/

define('MISSIVEAPP_MODULE_NAME', 'missiveapp');

/**
* Register activation module hook
*/
register_activation_hook(MISSIVEAPP_MODULE_NAME, 'missiveapp_module_activation_hook');

function missiveapp_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(MISSIVEAPP_MODULE_NAME, [MISSIVEAPP_MODULE_NAME]);
