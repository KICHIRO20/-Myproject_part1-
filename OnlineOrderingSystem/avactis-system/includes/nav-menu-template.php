<?php
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by HBWSL.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2010, HBWSL.
| unless specifically noted otherwise.
| =============================================
| This source code is released under the Avactis License Agreement.
| The latest version of this license can be found here:
| http://www.avactis.com/license.php
|
| By using this software, you acknowledge having read this license agreement
| and agree to be bound thereby.
|
 ***********************************************************************/
?><?php
   function asc_nav_menu( $args = array() ) {
   	global $application;
   	static $menu_id_slugs = array();

   	$defaults = array( 'menu' => '', 'container' => 'div', 'container_class' => '', 'container_id' => '', 'menu_class' => 'menu', 'menu_id' => '',
   	'echo' => true, 'fallback_cb' => 'asc_page_menu', 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '', 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
   	'depth' => 0, 'walker' => '', 'theme_location' => '' );
   	$r = array_merge( $defaults, $args );
   	$ul_class =  $r['menu_class'];
   	$container = $r['container'];
   	$container_id = $r['container_id'];
   	$container_class = $r['container_class'];
   	$menu_items = get_nav_menu_locations($args);
   	if (!is_array($menu_items)
   	|| empty($menu_items))
   	return '';
   	$skin = modApiFunc('Look_Feel', 'getCurrentSkin');
   	$path = dirname(dirname(dirname(__FILE__)))."/avactis-themes/".$skin."/cms/menu/default/item.tpl.html";
   	echo "<".$r['container']." class='$container_class'>"."<ul class='$ul_class'>";
   	foreach($menu_items as $k => $item)
   	{ ?>
<li class="" id="<?php echo 'menu_'.$item['menu_id']; ?>"><?php if(getMenuItemLink($item) == 'CMS_MENU_ITEM_TYPE_TEXT') {
   echo $item['item_name'];} else { ?><a
   href="<?php echo getMenuItemLink($item); ?>"><?php echo $item['item_name']; ?>
   </a> <?php } ?>
</li>
<?php } echo "</ul>"."</".$r['container'].">"; }
   /**
    * Generates the link for menu item
    */
   function getMenuItemLink($item)
   {
   	global $application;
   	$result = '';
   	switch($item['item_type'])
   	{
   		case CMS_MENU_ITEM_TYPE_TEXT:
   			$result = 'CMS_MENU_ITEM_TYPE_TEXT';
   			return $result;
   			break;
   		case CMS_MENU_ITEM_TYPE_URL:
   		case CMS_MENU_ITEM_TYPE_EXTERNAL_URL:
   			$result = $item['item_link'];
   			break;
   		case CMS_MENU_ITEM_TYPE_STATIC_PAGE:
   			loadClass('CCMSPageInfo');
   			$CMSPage = new CCMSPageInfo($item['item_link']);
   			$result = $CMSPage -> getCMSPageTagValue('link', array());
   			break;
   		case CMS_MENU_ITEM_TYPE_SYSTEM_PAGE:
   			$r = new Request();
   			$r -> setView($application -> getViewBySection($item['item_link']));
   			if (_ml_strtolower($item['item_link']) == 'productlist')
   			{
   				$r -> setCategoryID($item['param1']);
   				$r -> setAction('SetCurrCat');
   				$r -> setKey('category_id', $item['param1']);
   			}
   			if (_ml_strtolower($item['item_link']) == 'productinfo')
   			{
   				loadClass('CProductInfo');
   				$pr = new CProductInfo($item['param2']);
   				$item['param1'] = $pr -> chooseCategoryID();
   				$r -> setCategoryID($item['param1']);
   				$r -> setProductID($item['param2']);
   				$r -> setAction('SetCurrentProduct');
   				$r -> setKey('prod_id', $item['param2']);
   				$r -> setKey('category_id', $item['param1']);
   			}
   			$result = $r -> getURL();
   			break;
                    case CMS_MENU_ITEM_TYPE_CATEGORY_LIST:
                           $r = new Request();
   			$r -> setView($application -> getViewBySection($item['item_link']));
   			if (_ml_strtolower($item['item_link']) == 'productlist')
   			{
   				$r -> setCategoryID($item['param1']);
   				$r -> setAction('SetCurrCat');
   				$r -> setKey('category_id', $item['param1']);
   			}
                         $result = $r -> getURL();
                          break;
   	}
       return $result;
   }
   ?>