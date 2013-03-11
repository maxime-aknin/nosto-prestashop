# Nosto Tagging for PrestaShop

## Description

The module integrates the Nosto marketing automation service, that can produce personalized product recommendations on
the site.

The module adds the needed data to the site through PrestaShop's hook system. There are two types of data added by the
module; tagging blocks and nosto elements.

Tagging blocks are used to hold meta-data about products, categories, orders, shopping cart and customers on your site.
These types of blocks do not hold any visual elements, only meta-data. The meta-data is sent to the Nosto marketing
automation service when customers are browsing the site. The service then produces product recommendations based on the
information that is sent and displays the recommendations in the nosto elements.

Nosto elements are placeholders for the product recommendations coming from the Nosto marketing automation service. The
elements consist of only an empty div element that is populated with content from the Nosto marketing automation
service.

By default the module creates the following nosto elements:

* 3 elements for the product page
	* "Other Customers Were Interested In"
	* "You Might Also Like"
	* "Most Popular Products In This Category"
* 3 elements for the shopping cart page
	* "Customers Who Bought These Also Bought"
	* "Products You Recently Viewed"
	* "Most Popular Right Now"
* 2 elements for the product category page, top and bottom
	* "Most Popular Products In This Category"
	* "Your Recent History"
* 2 elements for the search results page, top and bottom
	* "Customers who searched '{search term}' viewed"
	* "Your Recent History"
* 2 elements for the sidebars, 1 left and 1 right
	* "Popular Products"
	* "Products You Recently Viewed"
* 2 elements for all pages, top and bottom
	* "Products containing '{keywords}'"
	* "Products You Recently Viewed"

Note that you can change what recommendations are shown in which nosto elements. You can also add additional elements
to the site by simply dropping in div elements of the following format:
'`<div class="nosto_element" id="{id of your choice}"></div>`'

The module also creates a new page called "Top Sellers". The page is added to the sites main menu automatically when
installing the module. The page contains only one Nosto element by default.

## Installation

Please refer to the PrestaShop documentation on how to get the module to appear in your installation admin section.

Once the module appears in your installation, you must install it into the store. Navigate to the "Modules" section and
locate the module, it will show up under the "Advertising & Marketing" section. The installation is done simply by
clicking the "install" button on the right by the module in the list.

The install procedure will create a new CMS page called "Top Sellers" and will try to put a menu item for the page in
the shop top menu, given that the default "Blocktopmenu" module is used to manage the menu. If you are using some other
module for your menu, then you will have to create the menu item yourself.

During the install the module also creates some new hooks for PrestaShop, namely "displayCategoryTop",
"displayCategoryFooter", "displaySearchTop" and "displaySearchFooter". You will need to implement these in your
installation in order for the module to work properly.

* displayCategoryTop
	* This hook should be placed above the product list on category pages
	* You need to add "`{if isset($HOOK_CATEGORY_TOP) && $HOOK_CATEGORY_TOP}{$HOOK_CATEGORY_TOP}{/if}`" in your themes
	catalog.tpl file at the appropriate location
	* You also need to include the smarty variable in the CategoryController class by adding
	"`'HOOK_CATEGORY_TOP' => Hook::exec('displayCategoryTop', array('category' => $this->category))`" in the initContent
	method
* displayCategoryFooter
	* This hook should be placed below the product list on category pages
	* You need to add "`{if isset(HOOK_CATEGORY_FOOTER) && HOOK_CATEGORY_FOOTER}{HOOK_CATEGORY_FOOTER}{/if}`" in your
	themes catalog.tpl file at the appropriate location
	* You also need to include the smarty variable in the CategoryController class by adding
	"`'HOOK_CATEGORY_FOOTER' => Hook::exec('displayCategoryFooter', array('category' => $this->category))`" in the
	initContent method
* displaySearchTop
	* This hook should be placed above the search result list on search pages
	* You need to add "`{if isset(HOOK_SEARCH_TOP) && HOOK_SEARCH_TOP}{HOOK_SEARCH_TOP}{/if}`" in your themes search.tpl
	file at the appropriate location
	* You also need to include the smarty variable in the SearchController class by adding
	"`'HOOK_SEARCH_TOP' => Hook::exec('displaySearchTop')`" in the initContent method
* displaySearchFooter
	* This hook should be placed below the search result list on search pages
	* You need to add "`{if isset(HOOK_SEARCH_FOOTER) && HOOK_SEARCH_FOOTER}{HOOK_SEARCH_FOOTER}{/if}`" in your themes
	search.tpl file at the appropriate location
	* You also need to include the smarty variable in the SearchController class by adding
	"`'HOOK_SEARCH_FOOTER' => Hook::exec('displaySearchFooter')`" in the initContent method

## Configuration

Once you have installed the module, you need to configure it. This is done by clicking the "Configure" link for the
module in the modules listing. This will open a new page with the module configuration that includes three settings:

* Server address
	* This is the server address for the Nosto marketing automation service
	* It will have the default value of "connect.nosto.com" and you do not need to change this
* Account name
	* This is your Nosto marketing automation service account name that you got when registering for the service
* Use default nosto elements
	* This setting controls if the module should create and output the default nosto elements for showing the product
	recommendations
	* You can disable the defaults if you want to use your own elements in your layout

## License

Open Software License ("OSL") v. 3.0

## Dependencies

PrestaShop version 1.5.x

## Changelog

* 1.0.0
	* Initial release
