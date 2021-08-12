<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'instantseo` (
            `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `value` TEXT NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            `date` VARCHAR(32) NOT NULL
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'instantseo_schemaentities` (
            `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `value` TEXT NOT NULL
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[] = "INSERT INTO `" . _DB_PREFIX_ . "instantseo` (id, name, value, date_add, date_upd)
VALUES
(1, 'SHOW_WEBPAGE', '1', now(), now()),
(2, 'SHOW_WEBSITE', '1', now(), now()),
(3, 'SHOW_WEBSITE_SEARCHBOX', '0', now(), now()),
(4, 'WEBSITE_KEYWORDS', '', now(), now()),
(5, 'CMS_PAGES', '', now(), now()),
(6, 'SHOW_WEBSITE_SITENAVIGATIONELEMENT', '0', now(), now()),
(7, 'SHOW_WEBSITE_BREADCRUMBS', '0', now(), now()),
(8, 'SHOW_ORGANIZATION', '0', now(), now()),
(9, 'SHOW_ORGANIZATION_LOGO', '0', now(), now()),
(10, 'ORGANIZATION_DESC', '', now(), now()),
(11, 'SHOW_ORGANIZATION_CONTACT', '0', now(), now()),
(12, 'ORGANIZATION_CONTACT_EMAIL', '', now(), now()),
(13, 'ORGANIZATION_CONTACT_TELEPHONE', '', now(), now()),
(14, 'SHOW_ORGANIZATION_FACEBOOK', '0', now(), now()),
(15, 'ORGANIZATION_FACEBOOK', '', now(), now()),
(16, 'SHOW_ORGANIZATION_TWITTER', '0', now(), now()),
(17, 'ORGANIZATION_TWITTER', '', now(), now()),
(18, 'SHOW_ORGANIZATION_INSTAGRAM', '0', now(), now()),
(19, 'ORGANIZATION_INSTAGRAM', '', now(), now()),
(20, 'SHOW_LOCALBUSINESS', '0', now(), now()),
(21, 'LOCALBUSINESS_TYPE', 'Store', now(), now()),
(22, 'LOCALBUSINESS_STORENAME', '', now(), now()),
(23, 'LOCALBUSINESS_DESC', '', now(), now()),
(24, 'LOCALBUSINESS_VAT', '', now(), now()),
(25, 'LOCALBUSINESS_PHONE', '', now(), now()),
(26, 'LOCALBUSINESS_STREET', '', now(), now()),
(27, 'LOCALBUSINESS_COUNTRY', '', now(), now()),
(28, 'LOCALBUSINESS_REGION', '', now(), now()),
(29, 'LOCALBUSINESS_CODE', '', now(), now()),
(30, 'LOCALBUSINESS_LOCALITY', '', now(), now()),
(31, 'LOCALBUSINESS_GPS_SHOW', '0', now(), now()),
(32, 'LOCALBUSINESS_GPS_LAT', '', now(), now()),
(33, 'LOCALBUSINESS_GPS_LON', '', now(), now()),
(34, 'OP_MODE', '0', now(), now()),
(35, 'SHOW_CATALOG_PRODUCTS', '0', now(), now());";

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
