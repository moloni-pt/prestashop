<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_2_0($module)
{
    $module->registerHook('displayOrderDetail');

    return true;
}
