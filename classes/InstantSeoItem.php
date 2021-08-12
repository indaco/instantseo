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

namespace InstantSeo;

class InstantSeoItem
{
    public $name = null;
    public $position = null;
    public $link_rewrite = null;

    public function __construct($name, $position = 0, $link_rewrite = "")
    {
        $this->name = $name;
        $this->position = $position;
        $this->link_rewrite = $link_rewrite;
    }

    public function createProperty($propertyName, $propertyValue)
    {
        $this->{$propertyName} = $propertyValue;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param null $position
     */
    public function setPosition($position): void
    {
        $this->position = $position;
    }

    /**
     * @return null
     */
    public function getLinkRewrite()
    {
        return $this->link_rewrite;
    }

    /**
     * @param null $link_rewrite
     */
    public function setLinkRewrite($link_rewrite): void
    {
        $this->link_rewrite = $link_rewrite;
    }
}
