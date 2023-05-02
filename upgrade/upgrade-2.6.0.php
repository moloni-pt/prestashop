<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_6_0($module): bool
{
    return $module->setMenu('MoloniTools', $module->l('Tools'), Tab::getIdFromClassName('MoloniTab'));
}
