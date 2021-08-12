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

class InstantSeoGetContentController
{

    /** @var null */
    protected $module = null;
    /** @var null */
    protected $file = null;
    /** @var Context|null */
    protected $context = null;
    /** @var null */
    protected $_path = null;
    /** @var \PrestaShopBundle\Translation\TranslatorComponent|null */
    protected $translator = null;

    public function __construct($module, $file, $path)
    {
        $this->module = $module;
        $this->file = $file;
        $this->context = Context::getContext();
        $this->_path = $path;
        $this->translator = $this->context->getTranslator();
        $this->cmsPages = CMS::getCMSPages($idLang = $this->context->language->id, $active = true, $idShop = $this->context->shop->id);
    }

    public function run()
    {
        return $this->processConfiguration();
    }

    /**
     * @return string
     *
     * @throws PrestaShopException
     */
    public function displayForm()
    {
        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        $helper = new HelperForm();
        // Module, token and currentIndex
        //$helper->module = $this;
        //$helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->module->name;
        $helper->table = $this->module->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        //$helper->identifier = $this->identifier;

        // Title and toolbar
        $helper->title = $this->module->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;
        $helper->submit_action = 'submit' . $this->module->name;

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $defaultLang,
        ];

        return $helper->generateForm([$this->makeConfigurationForm()]);
    }

    /**
     * @return array
     *
     * @throws PrestaShopException
     */
    protected function getConfigFieldsValues()
    {
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $vars = array();
        $idCMSPages = null;

        $results = $db->executeS('SELECT `name`, `value` FROM `' . _DB_PREFIX_ . $this->module->name . '`');

        foreach ($results as $row) {
            if ($row["name"] == "CMS_PAGES") {
                $idCMSPages = explode(", ", $row["value"]);
                if (isset($idCMSPages) && !empty($idCMSPages)) {
                    foreach ($idCMSPages as $idPage) {
                        $vars[$this->module->module_prefix . "CMS_PAGES_" . $idPage] = true;
                    }
                }
            }

            $vars[$this->module->module_prefix . $row["name"]] = $row["value"];
        }

        unset($db, $results);
        return $vars;
    }

    protected function makeConfigurationForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->translator->trans('Settings', [], 'Modules.InstantSeo.Settings'),
                    'icon' => 'icon-cogs',
                ],
                'tabs' => [
                    'WebSite' => 'Web Site',
                    'SNEBC' => 'SiteNavigation & Breadcrumbs',
                    'Organization' => 'Organization',
                    'LocalBusiness' => 'Local Business',
                    'Catalog' => 'Catalog',
                ],
                'input' => [
                    [
                        'tab' => 'WebSite',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Show Web Site', [], 'Modules.InstantSeo.ShowWebsiteLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_WEBSITE',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show Website structured data', [], 'Modules.InstantSeo.ShowWebsiteDesc'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => 'WebSite',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Show Web Page', [], 'Modules.InstantSeo.ShowWebpageLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_WEBPAGE',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show Website structured data', [], 'Modules.InstantSeo.ShowWebsiteDesc'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => 'WebSite',
                        'type' => 'textarea',
                        'label' => $this->translator->trans('Keywords', [], 'Modules.InstantSeo.WebsiteKeywordsLabel'),
                        'name' => $this->module->module_prefix . 'WEBSITE_KEYWORDS',
                        'prefix' => '<i class="icon icon-key"></i>',
                        'desc' => $this->translator->trans('Keywords describing your shop', [], 'Modules.InstantSeo.WebsiteKeywordsDesc'),
                        'col' => 3,
                    ],
                    [
                        'tab' => 'SNEBC',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Enable SiteNavigationElement', [], 'Modules.InstantSeo.ShowWebsiteSiteNavigationElementLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_WEBSITE_SITENAVIGATIONELEMENT',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show the Site Navigation Element structured data', [], 'Modules.InstantSeo.ShowWebsiteSiteNavigationElementDesc') . '<br />',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => 'SNEBC',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Enable Breadcrumbs', [], 'Modules.InstantSeo.ShowWebsiteBreadcrumbsLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_WEBSITE_BREADCRUMBS',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show the Breadcrumbs structured data', [], 'Modules.InstantSeo.ShowWebsiteBreadcrumbsDesc') . '<br />',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => "SNEBC",
                        'type' => 'checkbox',
                        'label' => $this->translator->trans('CMS Pages to be included', [], 'Modules.InstantSeo.CMSPagesLabel'),
                        'name' => $this->module->module_prefix . 'CMS_PAGES',
                        'desc' => $this->translator->trans('Select all you wish.', [], 'Modules.InstantSeo.CMSPagesDesc'),
                        'values' => array(
                            'query' => $this->cmsPages,
                            'id' => 'id_cms',
                            'name' => 'meta_title'
                        ),
                        'expand' => array(
                            'print_total' => count($this->cmsPages),
                            'default' => 'hide',
                            'show' => array('text' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'), 'icon' => 'plus-sign-alt'),
                            'hide' => array('text' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'), 'icon' => 'minus-sign-alt')
                        )
                    ],

                    [
                        'tab' => 'WebSite',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Enable Sitelinks Searchbox', [], 'Modules.InstantSeo.ShowWebsiteSitelinksSearchboxLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_WEBSITE_SEARCHBOX',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show the Website Sitelinks Searchbox structured data', [], 'Modules.InstantSeo.ShowWebsiteSitelinksSearchboxDesc') . '<br />',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => 'Organization',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Show Organization', [], 'Modules.InstantSeo.ShowOrganizationLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_ORGANIZATION',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show the Organization structured data', [], 'Modules.InstantSeo.ShowOrganizationDesc'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => 'Organization',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Logo', [], 'Modules.InstantSeo.ShowOrganizationLogoLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_ORGANIZATION_LOGO',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show the Organization Logo.', [], 'Modules.InstantSeo.ShowOrganizationLogoDesc'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => 'Organization',
                        'type' => 'text',
                        'label' => $this->translator->trans('Description:', [], 'Modules.InstantSeo.OrganizationDescLabel'),
                        'name' => $this->module->module_prefix . 'ORGANIZATION_DESC',
                        'desc' => $this->translator->trans('Short description of your business', [], 'Modules.InstantSeo.OrganizationDescDesc'),
                        'col' => 3
                    ],
                    [
                        'tab' => 'Organization',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Contact Point', [], 'Modules.InstantSeo.ShowOrganizationContactPointLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_ORGANIZATION_CONTACT',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show the Organization Contact Point structured data', [], 'Modules.InstantSeo.ShowOrganizationContactPointDesc'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => 'Organization',
                        'type' => 'text',
                        'label' => $this->translator->trans('Email', [], 'Modules.InstantSeo.OrganizationContactEmailLabel'),
                        'name' => $this->module->module_prefix . 'ORGANIZATION_CONTACT_EMAIL',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->translator->trans('Email address, if other than main', [], 'Modules.InstantSeo.OrganizationContactEmailDesc') . ': ' . Configuration::get('PS_SHOP_EMAIL'),
                        'col' => 3
                    ],
                    [
                        'tab' => 'Organization',
                        'type' => 'text',
                        'label' => $this->translator->trans('Telephone:', [], 'Modules.InstantSeo.OrganizationContactTelephoneLabel'),
                        'name' => $this->module->module_prefix . 'ORGANIZATION_CONTACT_TELEPHONE',
                        'prefix' => '<i class="icon icon-phone"></i>',
                        'desc' => $this->translator->trans('Organization telephone number (optional)', [], 'Modules.InstantSeo.OrganizationContactTelephoneDesc'),
                        'col' => 3
                    ],
                    [
                        'tab' => 'Organization',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Show Facebook page', [], 'Modules.InstantSeo.FacebookPageLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_ORGANIZATION_FACEBOOK',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show Facebook page URL in Organization structured data.', [], 'Modules.InstantSeo.FacebookPageDesc'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => 'Organization',
                        'type' => 'text',
                        'name' => $this->module->module_prefix . 'ORGANIZATION_FACEBOOK',
                        'label' => $this->translator->trans('Facebook Fan page URL:', [], 'Modules.InstantSeo.FacebookUrlLabel'),
                        'prefix' => '<i class="icon icon-facebook"></i>',
                        'desc' => ' https://www.facebook.com/YourPage',
                        'col' => 4
                    ],
                    [
                        'tab' => 'Organization',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Show Twitter page', [], 'Modules.InstantSeo.TwitterPageLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_ORGANIZATION_TWITTER',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show Twitter page URL in Organization structured data.', [], 'Modules.InstantSeo.TwitterPageDesc'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => 'Organization',
                        'type' => 'text',
                        'label' => $this->translator->trans('Twitter page URL:', [], 'Modules.InstantSeo.TwitterUrlLabel'),
                        'name' => $this->module->module_prefix . 'ORGANIZATION_TWITTER',
                        'prefix' => '<i class="icon icon-twitter"></i>',
                        'desc' => ' https://www.twitter.com/YourPage',
                        'col' => 4
                    ],
                    [
                        'tab' => 'Organization',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Show Instagram page', [], 'Modules.InstantSeo.InstagramPageLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_ORGANIZATION_INSTAGRAM',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show Instagram page URL in Organization structured data.', [], 'Modules.InstantSeo.InstagramPageDesc'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => 'Organization',
                        'type' => 'text',
                        'label' => $this->translator->trans('Instagram page URL:', [], 'Modules.InstantSeo.InstagramUrlLabel'),
                        'name' => $this->module->module_prefix . 'ORGANIZATION_INSTAGRAM',
                        'prefix' => '<i class="icon icon-instagram"></i>',
                        'desc' => ' https://www.instagram.com/YourPage',
                        'col' => 4
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Show LocalBusiness', [], 'Modules.InstantSeo.LocalBusinessLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_LOCALBUSINESS',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show LocalBusiness structured data', [], 'Modules.InstantSeo.LocalBusinessDesc'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'select',
                        'label' => $this->translator->trans('Select LocalBusiness type:', [], 'Modules.InstantSeo.LocalBusinessTypeLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_TYPE',
                        'desc' => $this->translator->trans('', [], 'Modules.InstantSeo.LocalBusinessTypeDesc'),
                        'required' => true,
                        'options' => [
                            'query' => [
                                [
                                    'option_id' => 'Store',
                                    'option_name' => 'Store',
                                ],
                                [
                                    'option_id' => 'LocalBusiness',
                                    'option_name' => 'LocalBusiness',
                                ],
                            ],
                            'id' => 'option_id',
                            'name' => 'option_name',
                        ],
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'text',
                        'label' => $this->translator->trans('Name of store:', [], 'Modules.InstantSeo.StoreNameLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_STORENAME',
                        'col' => 3
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'text',
                        'label' => $this->translator->trans('Description:', [], 'Modules.InstantSeo.LocalBusinessLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_DESC',
                        'desc' => $this->translator->trans('Short description of your business', [], 'Modules.InstantSeo.LocalBusinessDesc'),
                        'col' => 3
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'text',
                        'label' => $this->translator->trans('Tax id:', [], 'Modules.InstantSeo.LocalBusinessVATLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_VAT',
                        'col' => 3
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'text',
                        'label' => $this->translator->trans('Street:', [], 'Modules.InstantSeo.LocalBusinessStreetLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_STREET',
                        'col' => 3
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-flag"></i>',
                        'label' => $this->translator->trans('Country:', [], 'Modules.InstantSeo.LocalBusinessCountryLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_COUNTRY',
                        'col' => 3
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-globe "></i>',
                        'label' => $this->translator->trans('Region:', [], 'Modules.InstantSeo.LocalBusinessRegionLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_REGION',
                        'col' => 3
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'text',
                        'label' => $this->translator->trans('Postal code:', [], 'Modules.InstantSeo.LocalBusinessPostalCodeLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_CODE',
                        'col' => 3
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'text',
                        'label' => $this->translator->trans('Locality:', [], 'Modules.InstantSeo.LocalBusinessLocalityLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_LOCALITY',
                        'col' => 3
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-phone"></i>',
                        'label' => $this->translator->trans('Telephone:', [], 'Modules.InstantSeo.LocalBusinessPhoneLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_PHONE',
                        'desc' => $this->translator->trans('Telephone if other than main', [], 'Modules.InstantSeo.LocalBusinessPhoneDesc'),
                        'col' => 3
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Show GPS coordinates', [], 'Modules.InstantSeo.GPSLatLonLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_GPS_SHOW',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show Local Business GPS coordinates', [], 'Modules.InstantSeo.GPSLatLonDesc'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-map-marker"></i>',
                        'label' => $this->translator->trans('GPS latitude', [], 'Modules.InstantSeo.GPSLatLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_GPS_LAT',
                        'col' => 3
                    ],
                    [
                        'tab' => 'LocalBusiness',
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-map-marker"></i>',
                        'label' => $this->translator->trans('GPS longitude:', [], 'Modules.InstantSeo.GPSLonLabel'),
                        'name' => $this->module->module_prefix . 'LOCALBUSINESS_GPS_LON',
                        'col' => 3
                    ],
                    [
                        'tab' => 'Catalog',
                        'type' => 'switch',
                        'label' => $this->translator->trans('Show Products', [], 'Modules.InstantSeo.CatalogProductsLabel'),
                        'name' => $this->module->module_prefix . 'SHOW_CATALOG_PRODUCTS',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Show structured data for Products and Combinations', [], 'Modules.InstantSeo.CatalogProductsDesc'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Show', [], 'Modules.InstantSeo.Show'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('Hide', [], 'Modules.InstantSeo.Hide'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->translator->trans('Save', [], 'Modules.InstantSeo.Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];
    }

    protected function postProcess($form_values)
    {
        $db = DB::getInstance();
        foreach ($form_values as $key => $value) {
            if (!$db->update($this->module->name, [
                'value' => pSQL($value),
                'date_upd' => date('Y-m-d H:i:s'),
            ], 'name = "' . $key . '"', 0, false, false, true)
            ) {
                return false;
            }
        }

        return true;
    }

    protected function getListOfCMSSelectedPages()
    {
        $selected_cms_pages = array();
        foreach ($this->cmsPages as $index => $value) {
            $isSelected = Tools::getValue($this->module->module_prefix . 'CMS_PAGES_' . (string)($index + 1));
            if ($isSelected) {
                $selected_cms_pages[] = $value["id_cms"];
            }
        }
        $selected_cms_pages = implode(", ", $selected_cms_pages);
        return $selected_cms_pages;
    }

    /**
     * @return array
     */
    protected function getFormValues()
    {
        return [
            'op_mode' => (string)(Tools::getValue($this->module->module_prefix . 'OP_MODE')),
            'show_website' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_WEBSITE')),
            'show_webpage' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_WEBPAGE')),
            'show_website_searchbox' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_WEBSITE_SEARCHBOX')),
            'website_keywords' => (string)(Tools::getValue($this->module->module_prefix . 'WEBSITE_KEYWORDS')),
            'show_website_sitenavigationelement' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_WEBSITE_SITENAVIGATIONELEMENT')),
            'show_website_breadcrumbs' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_WEBSITE_BREADCRUMBS')),
            'cms_pages' => $this->getListOfCMSSelectedPages(),
            'show_organization' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_ORGANIZATION')),
            'show_organization_logo' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_ORGANIZATION_LOGO')),
            'organization_desc' => (string)(Tools::getValue($this->module->module_prefix . 'ORGANIZATION_DESC')),
            'show_organization_contact' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_ORGANIZATION_CONTACT')),
            'organization_contact_email' => (string)(Tools::getValue($this->module->module_prefix . 'ORGANIZATION_CONTACT_EMAIL')),
            'organization_contact_telephone' => (string)(Tools::getValue($this->module->module_prefix . 'ORGANIZATION_CONTACT_TELEPHONE')),
            'show_organization_facebook' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_ORGANIZATION_FACEBOOK')),
            'organization_facebook' => (string)(Tools::getValue($this->module->module_prefix . 'ORGANIZATION_FACEBOOK')),
            'show_organization_twitter' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_ORGANIZATION_TWITTER')),
            'organization_twitter' => (string)(Tools::getValue($this->module->module_prefix . 'ORGANIZATION_TWITTER')),
            'show_organization_instagram' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_ORGANIZATION_INSTAGRAM')),
            'organization_instagram' => (string)(Tools::getValue($this->module->module_prefix . 'ORGANIZATION_INSTAGRAM')),
            'show_localbusiness' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_LOCALBUSINESS')),
            'localbusiness_type' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_TYPE')),
            'localbusiness_storename' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_STORENAME')),
            'localbusiness_desc' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_DESC')),
            'localbusiness_vat' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_VAT')),
            'localbusiness_phone' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_PHONE')),
            'localbusiness_street' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_STREET')),
            'localbusiness_country' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_COUNTRY')),
            'localbusiness_region' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_REGION')),
            'localbusiness_code' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_CODE')),
            'localbusiness_locality' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_LOCALITY')),
            'localbusiness_gps_show' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_GPS_SHOW')),
            'localbusiness_gps_lat' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_GPS_LAT')),
            'localbusiness_gps_lon' => (string)(Tools::getValue($this->module->module_prefix . 'LOCALBUSINESS_GPS_LON')),
            'show_catalog_products' => (string)(Tools::getValue($this->module->module_prefix . 'SHOW_CATALOG_PRODUCTS')),
        ];
    }

    /**
     * @return string
     *
     * @throws PrestaShopException
     */
    private function processConfiguration()
    {
        $output = null;
        $form_values = null;

        if ((bool)Tools::isSubmit('submit' . $this->module->name)) {
            $form_values = $this->getFormValues();
            if (!$this->postProcess($form_values)) {
                $output .= $this->module->displayError($this->translator->trans('Invalid Configuration value', [], 'Modules.InstantSeo.SettingsNotUpdated'));
            } else {
                $output .= $this->module->displayConfirmation($this->translator->trans('Settings updated', [], 'Modules.InstantSeo.SettingsUpdated'));
            }
        }
        unset($form_values);

        return $output . $this->displayForm();
    }
}
