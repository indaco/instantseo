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

require_once _PS_MODULE_DIR_ . "instantseo/classes/InstantSeoItemFactory.php";
use InstantSeo\InstantSeoItemFactory;

class InstantSeoDisplayBeforeBodyClosingTagController
{
    /** @var $module */
    protected $module = null;
    /** @var $file */
    protected $file = null;
    /** @var Context|null */
    protected $context = null;
    /** @var $_path */
    protected $_path = null;
    /** @var $db */
    private $db = null;
    /** @var string */
    private $production_file = null;

    public function __construct($module, $file, $path)
    {
        $this->module = $module;
        $this->file = $file;
        $this->context = Context::getContext();
        $this->_path = $path;
        $this->db = Db::getInstance(_PS_USE_SQL_SLAVE_);
    }

    public function run()
    {
        return $this->runner();
    }

    protected function runner()
    {
        $data = array();
        $socials = array();
        $idCMSPages = $this->getCMSPagesList();
        $results = $this->db->executeS('SELECT `name`, `value` FROM `' . _DB_PREFIX_ . $this->module->name . '`');


        foreach ($results as $row) {
            if ($row["name"] == "WEBSITE_KEYWORDS") {
                $data[Tools::strtolower($row["name"])] = $this->getKeywordsArray($row["value"]);
            } elseif ($row["name"] == "ORGANIZATION_CONTACT_EMAIL") {
                $data[Tools::strtolower($row["name"])] = $this->getOrganizationEmail($row["value"]);
            } elseif ($row["name"] == "SHOW_ORGANIZATION") {
                $data[Tools::strtolower($row["name"])] = $row["value"];
            } elseif ($row["name"] == "SHOW_ORGANIZATION_FACEBOOK"
                || $row["name"] == "ORGANIZATION_FACEBOOK"
                || $row["name"] == "SHOW_ORGANIZATION_TWITTER"
                || $row["name"] == "ORGANIZATION_TWITTER"
                || $row["name"] == "SHOW_ORGANIZATION_INSTAGRAM"
                || $row["name"] == "ORGANIZATION_INSTAGRAM") {
                $socials[$row["name"]] = $row["value"];
            } elseif ($row["name"] == "SHOW_WEBSITE_SITENAVIGATIONELEMENT") {
                $data[Tools::strtolower($row["name"])] = $row["value"];
                $data["menu_tree"] = $this->buildElementList($idCMSPages);
            } else {
                $data[Tools::strtolower($row["name"])] = $row["value"];
            }
        }

        $data["organization_socialpages"] = $this->getOrganizationSocialPages($socials);

        $this->context->smarty->assign($data);
        $output = $this->module->fetch('module:' . $this->module->hooks_tpl_path . '/footer.tpl');
        // write to file
        //file_put_contents($this->production_file,$output);
        return $output;
    }

    protected function getKeywordsArray($keywords)
    {
        $keywords_array = [];
        $keywords = str_replace(', ', ',', $keywords);
        if (!empty($keywords)) {
            $keywords_array = explode(',', $keywords);
        }

        return $keywords_array;
    }

    protected function getOrganizationEmail($value)
    {
        $organization_email_address = '';
        if (!empty($value)) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $organization_email_address = $value;
            }
        } else {
            $organization_email_address = Configuration::get('PS_SHOP_EMAIL');
        }

        return $organization_email_address;
    }

    protected function getOrganizationSocialPages($values)
    {
        $social_pages = [];

        $organization_show_facebookpage = $values["SHOW_ORGANIZATION_FACEBOOK"];
        $organization_facebookpage = $values["ORGANIZATION_FACEBOOK"];
        if ($organization_show_facebookpage) {
            array_push($social_pages, $organization_facebookpage);
        }

        $organization_show_twitterpage = (bool)$values["SHOW_ORGANIZATION_TWITTER"];
        $organization_twitterpage = $values["ORGANIZATION_TWITTER"];
        if ($organization_show_twitterpage) {
            array_push($social_pages, $organization_twitterpage);
        }

        $organization_show_instagrampage = (bool)$values["SHOW_ORGANIZATION_INSTAGRAM"];
        $organization_instagrampage = $values["ORGANIZATION_INSTAGRAM"];
        if ($organization_show_instagrampage) {
            array_push($social_pages, $organization_instagrampage);
        }
        return $social_pages;
    }

    protected function getCMSPagesList()
    {
        $idCMSPages = $this->db->getRow('SELECT `value` FROM `' . _DB_PREFIX_ . $this->module->name . '` WHERE `name` = "CMS_PAGES"');
        return explode(", ", $idCMSPages["value"]);
    }

    protected function buildElementList($idCMSPages = null): array
    {
        $idLanguage = $this->context->language->id;
        $menu_tree = array();

        // HOMEPAGE
        $home_page = InstantSeoItemFactory::create("HOME", 1, "");
        array_push($menu_tree, get_object_vars($home_page));

        // CMS Pages
        $cmsPagesAdded = false;
        if (isset($idCMSPages) && $idCMSPages[0] != "") {
            $last = end($menu_tree);
            $cmsPagesAdded = true;
            foreach (CMS::getLinks($idLanguage, $idCMSPages) as $index => $link) {
                $page = InstantSeoItemFactory::create($link["meta_title"], (string)(round($last["position"]) + ($index+1)), $link["link_rewrite"]);
                array_push($menu_tree, get_object_vars($page));
            }
        }

        // Contact Page
        $last = end($menu_tree);
        $contact_controller_url = $this->context->link->getPageLink('contact', true);
        $contact_page_name = str_replace('/', '', parse_url($contact_controller_url, PHP_URL_PATH));

        $contact_page = InstantSeoItemFactory::create();
        $contact_page->setName(ucwords($contact_page_name));
        $contact_page->setLinkRewrite($contact_page_name);

        if ($cmsPagesAdded) {
            $contact_page->setPosition(round($last["position"]) + 1);
        } else {
            $contact_page->setPosition(round($last["position"]));
        }
        array_push($menu_tree, get_object_vars($contact_page));

        return $menu_tree;
    }
}
