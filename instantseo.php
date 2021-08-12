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

if (!defined('_PS_VERSION_')) {
    exit;
}

class InstantSeo extends Module
{
    /** @var string Module prefix name */
    public $module_prefix = null;
    /** @var string Module SQL path */
    public $sql_path = null;
    /** @var string module template files path */
    public $hooks_tpl_path = null;

    public function __construct()
    {
        $this->name = 'instantseo';
        $this->tab = 'seo';
        $this->version = '1.0.0';
        $this->author = 'indaco';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.6',
            'max' => '1.7.7.6',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('InstantSEO - Schema.org types builder', [], 'Modules.InstantSeo.Admin');
        $this->description = $this->trans('Prestashop module to easily add Schema.org Structured Data to your shop.', [], 'Modules.InstantSeo.Admin');
        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.InstantSeo.Admin');

        $this->module_prefix = Tools::strtoupper($this->name) . '_';
        $this->sql_path = dirname(__FILE__) . '/sql/';
        $this->hooks_tpl_path = $this->name . '/views/templates/hook';
    }

    public function install(): bool
    {

        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install()
            && $this->registerHook('displayBeforeBodyClosingTag')
            && Configuration::updateValue($this->module_prefix . 'VERSION', $this->version);
    }

    public function uninstall(): bool
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall()
            && Configuration::deleteByName($this->module_prefix . 'VERSION');
    }


    /**
     * The module makes use of the new Prestashop 1.7.6 translations system
     *
     * @return bool
     */
    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    public function getHookController($hook_name)
    {
        // Include the controller file
        require_once dirname(__FILE__) . '/controllers/hook/' . $hook_name . '.php';
        // Build the controller name
        $controller_name = get_class($this) . ucwords($hook_name) . 'Controller';
        // Create a new instance for the controller
        $controller = new $controller_name($this, __FILE__, $this->_path);

        return $controller;
    }

    public function hookDisplayBeforeBodyClosingTag($params)
    {
        $controller = $this->getHookController('displayBeforeBodyClosingTag');
        return $controller->run();
    }

    public function getContent()
    {
        $controller = $this->getHookController('getContent');
        return $controller->run();
    }
}
