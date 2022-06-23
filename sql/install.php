<?php
/**
 * 2020 - moloni.pt
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 * DISCLAIMER 
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Nuno Almeida
 *  @copyright Nuno Almeida
 *  @license   https://creativecommons.org/licenses/by-nd/4.0/  Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0)
 */

$sql = array();


# Correr queries de inicio
# Invoice Status
#	0 - Inserido como rascunho
#	1 - Inserido fechado
#	2 - Inserido fechado e enviado
#	3 - Inserido com erro
#	4 - Não gerar documento



$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."moloni` (
			  `id` int(11) NOT null AUTO_INCREMENT,
			  `access_token` varchar(250) CHARACTER SET utf8 NOT null,
			  `refresh_token` varchar(250) CHARACTER SET utf8 NOT null,
			  `company_id` int(11) NOT null,
			  `date_login` varchar(250) CHARACTER SET utf8 NOT null,
			  `date_expire` varchar(250) CHARACTER SET utf8 NOT null,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."moloni_configs` (
					  `id` int(11) NOT null AUTO_INCREMENT,
					  `label` varchar(250) CHARACTER SET utf8 NOT null,
					  `name` varchar(250) CHARACTER SET utf8 NOT null,
					  `description` text NOT null,
					  `value` varchar(250) CHARACTER SET utf8 NOT null,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."moloni_invoices` (
					  `id` int(11) NOT null AUTO_INCREMENT,
					  `order_id` varchar(250) CHARACTER SET utf8 NOT null,
					  `order_total` varchar(250) CHARACTER SET utf8 NOT null,
					  `invoice_id` varchar(250) CHARACTER SET utf8 NOT null,
					  `invoice_total` varchar(250) CHARACTER SET utf8 NOT null,
					  `invoice_date` DATE,
					  `invoice_status`int(11) NOT null,
					  `value` int(11) NOT null,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";


foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
