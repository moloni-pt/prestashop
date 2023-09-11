<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_0_0($module): bool
{
    /** Install new tab for logs */
    $module->setMenu('MoloniLogs', $module->l('Logs'), Tab::getIdFromClassName('MoloniTab'));

    /** Create new table for logs */
    $query = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."moloni_logs` (
                      `id` INT(11) NOT NULL AUTO_INCREMENT,
                      `log_level` VARCHAR(100) NULL,
                      `company_id` INT,
                      `message` TEXT,
                      `context` TEXT,
                      `created_at` TIMESTAMP default CURRENT_TIMESTAMP,
					  PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

    Db::getInstance()->execute($query);

    return true;
}
