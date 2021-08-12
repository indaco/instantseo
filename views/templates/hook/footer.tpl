{*
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
*}

{if $op_mode eq '1'}
    <!-- production mode -->
{/if}

{if $show_website}
    <!-- WebSite data on every page-->
    <script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "WebSite",
		"url": "{$urls.base_url|escape:'htmlall':'UTF-8'}",
		"image": {
			"@type": "ImageObject",
			"url": "{$urls.shop_domain_url|escape:'htmlall':'UTF-8'}{$shop.logo|escape:'htmlall':'UTF-8'}"
		},
		"potentialAction": {
      		"@type": "SearchAction",
      		"target": "{'--search_term_string--'|str_replace:'{search_term_string}':$link->getPageLink('search',true,null,['search_query'=>'--search_term_string--'])|escape:'htmlall':'UTF-8'}",
     		"query-input": "required name=search_term_string"
    	}
{if $website_keywords|@count gt 0}
		,
		"keywords": [
{foreach item=keyword key=id_keyword from=$website_keywords }
		"{$keyword|escape:'htmlall':'UTF-8'}"{if !$keyword@last},{else}{/if}

        {/foreach}
	]
{/if}
	}
	</script>
{/if}

{if $show_website_sitenavigationelement}

    {if isset($menu_tree) and $menu_tree|@count gt 0 }
    <!-- SiteNavigationElement on every page -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ItemList",
        "itemListElement": [
        {foreach item=menuitem key=id_menuitem from=$menu_tree}
        {
            "@context": "https://schema.org",
            "@type": "SiteNavigationElement",
            "position": "{$menuitem["position"]|escape:'htmlall':'UTF-8'}",
            "name": "{$menuitem["name"]|escape:'htmlall':'UTF-8'}",
            "url": "{$urls.base_url|escape:'htmlall':'UTF-8'}{$menuitem["link_rewrite"]|escape:'htmlall':'UTF-8'}"
        }{if !$menuitem@last},{else}{/if}
        {/foreach}
        ]
    }
    </script>
    {/if}

{/if}

{if $show_website_breadcrumbs}

{if isset($menu_tree) and $menu_tree|@count gt 0 }
    <!-- Breadcrumbs on every page -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
        {foreach item=menuitem key=id_menuitem from=$menu_tree}
        {
            "@context": "https://schema.org",
            "@type": "ListItem",
            "position": "{$menuitem["position"]|escape:'htmlall':'UTF-8'}",
            "name": "{$menuitem["name"]|escape:'htmlall':'UTF-8'}",
            "item": "{$urls.base_url|escape:'htmlall':'UTF-8'}{$menuitem["link_rewrite"]|escape:'htmlall':'UTF-8'}"
        }{if !$menuitem@last},{else}{/if}
        {/foreach}
        ]
    }

    </script>
    {/if}

{/if}

{if $show_localbusiness}
    <!-- LocalBusiness data on every page -->
    <script type="application/ld+json">
	{
		"@context": "http://schema.org",
		"@type": "{$localbusiness_type|escape:'htmlall':'UTF-8'}",
		"name":"{$localbusiness_storename|escape:'htmlall':'UTF-8'}",
{if $localbusiness_desc}
		"description": "{$localbusiness_desc|escape:'htmlall':'UTF-8'}",
{/if}
		"image": "{$urls.shop_domain_url|escape:'htmlall':'UTF-8'}{$shop.logo|escape:'htmlall':'UTF-8'}",
		"@id": "{$urls.base_url|escape:'htmlall':'UTF-8'}",
		"url": "{$urls.base_url|escape:'htmlall':'UTF-8'}",
{if $localbusiness_vat}
		"vatID": "{$localbusiness_vat|escape:'htmlall':'UTF-8'}",
{/if}
		"paymentAccepted":"PayPal, AmazonPay, Credit Card",
{if $localbusiness_phone}
		"telephone" : "{$localbusiness_phone|escape:'htmlall':'UTF-8'}",
{/if}
		"address": {
			"@type": "PostalAddress",
			"streetAddress": "{$localbusiness_street|escape:'htmlall':'UTF-8'}",
			"addressLocality": "{$localbusiness_locality|escape:'htmlall':'UTF-8'}",
{if $localbusiness_region}
			"addressRegion": "{$localbusiness_region|escape:'htmlall':'UTF-8'}",
{/if}
			"addressCountry": "{$localbusiness_country|escape:'htmlall':'UTF-8'}",
			"postalCode": "{$localbusiness_code|escape:'htmlall':'UTF-8'}"
		},
{if $localbusiness_gps_show && $localbusiness_gps_lat && $localbusiness_gps_lon}
		"geo": {
			"@type": "GeoCoordinates",
			"latitude": "{$localbusiness_gps_lat|escape:'htmlall':'UTF-8'}",
			"longitude": "{$localbusiness_gps_lon|escape:'htmlall':'UTF-8'}"
		},
{/if}
		"openingHours": ["09:00-19:00"]
	}
	</script>
{/if}


{if $show_catalog_products}
    <!-- Catalog Product page-->
    {if $page.page_name == 'product'}
        <script type="application/ld+json">
	{
		"@context": "http://schema.org/",
		"@type": "Product",
		"name": "{$product->name|escape:'htmlall':'UTF-8'}",
{if $product_manufacturer->name}
		 "brand": {
			"@type": "Thing",
			"name": "{$product_manufacturer->name|escape:'htmlall':'UTF-8'}"
		},
{/if}
		"description": "{($product->description_short)|strip_tags|escape:'htmlall':'UTF-8'}",
{if $product->reference}
		"sku": "{$product->reference|escape:'htmlall':'UTF-8'}",
{/if}
{if $product->reference}
		"mpn": "{$product->id|escape:'htmlall':'UTF-8'}",
{/if}
{if $product.images|@count gt 1}
		"image": [
{foreach from=$product.images item=image name=thumbs}
		"{$image.bySize.medium_default.url|escape:'htmlall':'UTF-8'}"{if !$image@last},{else}{/if}
            {/foreach}
		],
{/if}
{if isset($nbComments) && $nbComments && $ratings.avg}
		"aggregateRating": {
			"@type": "AggregateRating",
			"ratingValue": "{($ratings.avg)|round:1|escape:'htmlall':'UTF-8'}",
			"reviewCount": "{$nbComments|escape:'htmlall':'UTF-8'}"
		},
{/if}
{if empty($combinations)}
		"offers": {
			"@type": "Offer",
			"priceCurrency": "{$currency.iso_code|escape:'htmlall':'UTF-8'}",
			"name": "{$product->name|escape:'htmlall':'UTF-8'}",
			"price": "{($product->price)|round:'2'|escape:'htmlall':'UTF-8'}",
			"image": "{$link->getImageLink($product->link_rewrite, $product.cover.id_image, 'home_default')|escape:'htmlall':'UTF-8'}",
{if isset($product->ean13)}
			"gtin13": "{$product->ean13|escape:'htmlall':'UTF-8'}",
{elseif isset($product->upc)}
			"gtin13": "0{$product->upc|escape:'htmlall':'UTF-8'}",
{/if}
			"sku": "{$product->reference|escape:'htmlall':'UTF-8'}",
			"availability":{if $product->quantity > 0} "http://schema.org/InStock"{else} "http://schema.org/OutOfStock"{/if},
			"url": "{$urls.current_url|escape:'htmlall':'UTF-8'}",
			"seller": {
				"@type": "Organization",
				"name": "{$shop.name|escape:'htmlall':'UTF-8'}"
			}
		}
{else}
		"offers": [
{foreach key=id_product_combination item=combination from=$combinations}
			{
				"@type": "Offer",
				"name": "{$product->name|escape:'htmlall':'UTF-8'} - {$combination.reference|escape:'htmlall':'UTF-8'}",
				"priceCurrency": "{$currency.iso_code|escape:'htmlall':'UTF-8'}",
				"price": "{(Product::getPriceStatic($product->id, true, $id_product_combination))|round:'2'|escape:'htmlall':'UTF-8'}",
				"image": "{if $combination.id_image > 0}{$link->getImageLink($product->link_rewrite, $combination.id_image, 'home_default')|escape:'htmlall':'UTF-8'}{else}{$link->getImageLink($product->link_rewrite, $product.cover.id_image, 'home_default')|escape:'htmlall':'UTF-8'}{/if}",
{if isset($combination.ean13)}
				"gtin13": "{$combination.ean13|escape:'htmlall':'UTF-8'}",
{elseif isset($combination.upc)}
				"gtin13": "0{$combination.upc|escape:'htmlall':'UTF-8'}",
{/if}
				"sku": "{$combination.reference|escape:'htmlall':'UTF-8'}",
				"availability": {if $combination.quantity > 0}"http://schema.org/InStock"{else}"http://schema.org/OutOfStock"{/if},
				"url": "{$urls.current_url|escape:'htmlall':'UTF-8'}",
				"seller": {
					"@type": "Organization",
					"name": "{$shop.name|escape:'htmlall':'UTF-8'}"
				}
			} {if !$combination@last},{else}{/if}
            {/foreach}

		]
{/if}
	}
	</script>
    {/if}
{/if}


{if $show_organization}
    <!-- Organization data on every page -->
    <script type="application/ld+json">
	{
		"@context" : "http://schema.org",
		"@type" : "Organization",
		"name" : "{$shop.name|escape:'htmlall':'UTF-8'}",
		"description": "{$organization_desc|escape:'htmlall':'UTF-8'}",
		"url" : "{$urls.base_url|escape:'htmlall':'UTF-8'}",
{if organization_show_logo}
		"logo" : {
			"@type":"ImageObject",
			"url":"{$urls.shop_domain_url|escape:'htmlall':'UTF-8'}{$shop.logo|escape:'htmlall':'UTF-8'}"
		},
{/if}
{if organization_show_contact}
		"contactPoint" : {
			"type" : "ContactPoint",
			"email" : "{$organization_contact_email|escape:'htmlall':'UTF-8'}",
{if $organization_contact_telephone}
			"telephone" : "{$organization_contact_telephone|escape:'htmlall':'UTF-8'}",
{/if}
			"contactType" : "customer service"
		}
{/if}
{if !empty($organization_socialpages) and $organization_socialpages|@count gt 1}
		,
		"sameAs": [
{foreach key=id_social_page from=$organization_socialpages item=page}
		"{$page|escape:'htmlall':'UTF-8'}"{if !$page@last},{else}{/if}
        {/foreach}
		]
{elseif !empty($organization_socialpages)}
		,
		"sameAs": "{$organization_socialpages[0]|escape:'htmlall':'UTF-8'}"
{/if}
	}

	</script>
{/if}