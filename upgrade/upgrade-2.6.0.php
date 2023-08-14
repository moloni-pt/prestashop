<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_6_0($module): bool
{
    $module->registerHook('addWebserviceResources');
    $module->setMenu('MoloniTools', $module->l('Tools'), Tab::getIdFromClassName('MoloniTab'));

    return true;
}
