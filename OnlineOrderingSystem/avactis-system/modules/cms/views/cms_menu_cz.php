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

/**
 * @package CMS
 * @author Sergey Kulitsky
 *
 */

class CMSMenu
{
    function CMSMenu()
    {
        global $application;

        $this -> mTmplFiller = new TemplateFiller();

        $this -> _templates = array(
            'container' => 'CMSMenuContainer',
            'item'      => 'CMSMenuItem',
        	'categorymenucontainer' => 'CategoryMenuContainer',
        	'categorymenuitem' => 'CategoryMenuItem'
        );

        $this -> NoView = false;
        if ($application -> issetBlockTagFatalErrors('CMSMenu'))
            $this -> NoView = true;
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file' => 'cms-menu-block.ini',
            'files'       => array(
                'CMSMenuContainer' => TEMPLATE_FILE_SIMPLE,
                'CMSMenuItem'      => TEMPLATE_FILE_SIMPLE,
            	'CategoryMenuContainer' => TEMPLATE_FILE_SIMPLE,
            	'CategoryMenuItem' => TEMPLATE_FILE_SIMPLE
            ),
            'options'     => array(
            )
        );
        return $format;
    }

    /**
     * The output of the Viewer
     * Params: the following optional params can be accepted
     *      0: menu_id/menu_system_name:
     *      1: templates to be used for output
     */
    function output()
    {
        global $application;

        if ($this -> NoView)
            return '';

        $menu_id = 0;
        $template_name = '';

        // getting the menu_id from the params if any
        if (func_num_args() > 0)
            $menu_id = func_get_arg(0);

        // getting the templates if specified
        if (func_num_args() > 1)
            $template_name = func_get_arg(1);

        // getting the menu data
        $this -> _Menu_Data = modApiFunc('CMS', 'searchMenu',
                                         array('menu_id' => $menu_id,
                                               'menu_index' => $menu_id));
        // getting review data
        if (!empty($this -> _Menu_Data))
        {
            // the menu_id is specified and valid
            $this -> _Menu_Data = array_pop($this -> _Menu_Data);
            $menu_id = $this -> _Menu_Data['menu_id'];
            $this -> _Menu_Data['items'] = modApiFunc('CMS', 'getMenuItems',
                                                      $menu_id);
        }
        else
        {
            // invalid menu
            return '';
        }

        // setting the template engine
        $template_block = $application -> getBlockTemplate('CMSMenu');
        if ($template_name)
        {
            // assuming the template_name is the directory name
            $tmp = explode('/', $template_block['template']['directory']);
            array_pop($tmp);
            $tmp[] = $template_name;
            $template_block['template']['directory'] = implode('/', $tmp);
        }
        $this -> mTmplFiller -> setTemplate($template_block);

        // registering tags
        $_tags = array(
            'LocalMenuTitle' => $this -> _Menu_Data['menu_name'],
            'LocalMenuItems' => $this -> outputMenuItems(),
        	'MainMenuItems' => '',
		'MobileMainMenuItems' => ''
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);
        $skin = modApiFunc('Look_Feel', 'getCurrentSkin');
        $category_menu = false;
        $category_menu = apply_filters('category_menu_container');
        if(($this -> _Menu_Data['menu_index'] == 'main_menu') && ($category_menu === true))
        {
        	return $this -> mTmplFiller -> fill($this -> _templates['categorymenucontainer']);
        }
        else
       {
        	return $this -> mTmplFiller -> fill($this -> _templates['container']);
    	}
    }

    /**
     * Outputs menu items
     */
    function outputMenuItems()
    {
        global $application;

        $result = '';

        if (!is_array($this -> _Menu_Data['items'])
            || empty($this -> _Menu_Data['items']))
            return $result;

        foreach($this -> _Menu_Data['items'] as $k => $item)
        {
            $_tags = array(
                'LocalMenuItemLink'  => $this -> getMenuItemLink($item),
                'LocalMenuItemName'  => $item['item_name'],
                'LocalMenuItemType'  => $item['item_type'],
                'LocalMenuItemFirst' => ($k == 0),
                'LocalMenuItemLast'  => ($k == count($this -> _Menu_Data['items']) - 1)
            );
            $this -> _Template_Contents = $_tags;
            $application -> registerAttributes($this -> _Template_Contents);

            $result .= $this -> mTmplFiller -> fill($this -> _templates['item']);
        }

        return $result;
    }

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
                $result = '';
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
        }

        return $result;
    }

    function displayCategories()
    {
	    $categories = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1, false);
	    $slicedcats = array_slice($categories,0,50);

	    $count = count($categories);
	    $value = "";
	    foreach($slicedcats as $category)
	    {
		    if(($category['level'] == 0) || ($category['level'] == 1) )
		    {
			    $catid = $category["id"];
			    $catmainlink = getCategoryLink($catid);
			    $cathomelink = getPageURL('Categories {1}');

			    $value .= '';

			    if($catid == 1)
			    {
				    $value .= '<div class="pt_menu"><div class="parentMenu"><a href="' . $cathomelink . '"><span>'.$category['name'].'</span></a>
					    </div>';
			    }
			    else
			    {
				    $value .='<div class="pt_menu"><div class="parentMenu"><a href="' . $catmainlink . '"><span>'.$category['name'].'</span></a>
					    </div>';
			    }
			    if((modApiFunc("Catalog", "hasOnlineSubcategories",$category['id'])) && ($category['level'] > 0))
			    {
					$value .= '<div id="popup3" class="popup" style="display: none; width: 1228px;"><div class="block1" id="block13"><div class="column first col1">';
				    $value .= $this->subcatlevel($category['id'],1,$slicedcats);	// Looping for subcategories
					$value .= '</div></div></div>';
			    }

			    $value .= "</div>";
		    }
	    }
	    return $value;
    }

    function displayMobileCategories()
    {
	    $categories = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1, false);
	    $slicedcats = array_slice($categories,0,1000);

	    $count = count($categories);
	    $value = "";
	    foreach($slicedcats as $category)
	    {
		    if(($category['level'] == 0) || ($category['level'] == 1) )
		    {
			    $catid = $category["id"];
			    $catmainlink = getCategoryLink($catid);
			    $cathomelink = getPageURL('Categories {1}');

			    $value .= '';

			    if($catid == 1)
			    {
				    $value .= '<li class="level0 nav-1 level-top first parent"><a class="level-top" href="' . $cathomelink . '"><span>'.$category['name'].'</span></a>
					    ';
			    }
			    else
			    {
				    $value .='<li class="level0 nav-1 level-top first parent"><a class="level-top" href="' . $catmainlink . '"><span>'.$category['name'].'</span></a>
					    ';
			    }
			    if((modApiFunc("Catalog", "hasOnlineSubcategories",$category['id'])) && ($category['level'] > 0))
			    {
					$value .= '<ul class="level1" style="display: none;">';
				    $value .= $this->Mobilesubcatlevel($category['id'],1);	// Looping for subcategories
					$value .= '</ul>';
			    }

			    $value .= "</li>";
		    }
	    }
	    return $value;
    }

    function Mobilesubcatlevel($catid,$catlevel)
    {
	    $lvl2subcats = modApiFunc("Catalog", "getSubCategories",$catid);
	    // Checking for subcategories which have level greater than 1
	    if($catlevel>1)
	    {
		    $catname = getCategoryName($catid);

		    $catlink = getCategoryLink($catid);
		    $value = '';
		    $value .= '<li class="level2 nav-1-1"><a href="' . $catlink . '"><span>' . $catname . '</span></a>';
	    }

	    //Get subcategories for level2 and below
	    $count = count($lvl2subcats)-1;
	    while($count>=0)
	    {
		    $value.= $this->Mobilesubcatlevel(getCategoryID($lvl2subcats[$count]["category_id"]),3);
		    $count = $count -1;
	    }

	    if($catlevel>1)
	    {
		    $value .= "</li>";
	    }

	    return $value;
    }

    function subcatlevel($catid,$catlevel,$slicedcats=array())
    {
	    $lvl2subcats = modApiFunc("Catalog", "getSubCategories",$catid);
	    // Checking for subcategories which have level greater than 1

	    $mainkey=array();
	    $mainvalue=array();
	    $submainvalue=array();
	// $catarray = array();
  	    if((!empty($lvl2subcats)))
	    {
		$categories = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1, false);
		$slicedcats = array_slice($categories,0,sizeof($categories)-1);//added by swapnil


		foreach($slicedcats as $key=> $val)
		{
  			if($val['id']==$catid)
			{
				if ($val['level'] >= 2)
				{
					$anotherkey = $key;
				}
				else {
					$startkey=$key;
					$startkey=$startkey+1;
				}
			}

		}

		for ($i=0; $i<sizeof($lvl2subcats); $i++)
		{
			$mainkey[]=$i;
			if ($slicedcats[$startkey]['level'] > 2 )
			{
				$startkey=$startkey+2;
				$mainvalue[]=$slicedcats[$startkey]['id'];
			}
			else
			{
				$mainvalue[]=$slicedcats[$startkey]['id'];
			}
			// $anotherkey = $anotherkey + 1;
			$startkey=$startkey+1;
		}
		$c=array_combine($mainkey,$mainvalue);
		$lvl2subcats=array_reverse($c);
	    }
	    if($catlevel>1)
	    {
		    $catname = getCategoryName($catid);
		    $catlink = getCategoryLink($catid);
		    $value = '';
		    $value .= '<div class="itemMenu level1"><a class="itemMenuName level1" href="' . $catlink . '"><span>' . $catname . '</span></a>';
	    }

	    //Get subcategories for level2 and below
	    $count = count($lvl2subcats)-1;
	    while($count>=0 && $catlevel < 2)
	    {
		    $value.= $this->subcatlevel(getCategoryID($lvl2subcats[$count]),3);
		    $count = $count - 1;
	    }

	    if($catlevel>1)
	    {
		    $value .= "</div>";
	    }

	    return $value;
    }

    function displayDefaultCategories()
    {
	    $categories = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1, false);
	    $_Cat_Info = new CCategoryInfo(1);  // Category Info get for Home Category
	    $countOfSubCategory=$_Cat_Info->getCategoryTagValue('subcategoriesnumber')+1; //Count total number of item in the subcategory of Home category
            $slicedcats = array_slice($categories,0,$countOfSubCategory);
	    $count = count($categories);
	    $value = "";
	    foreach($slicedcats as $category)
	    {
		    if(($category['level'] == 0) || ($category['level'] == 1))
		    {
			    $catid = $category["id"];
			    $catmainlink = getCategoryLink($catid);
			    $cathomelink = getPageURL('Categories {1}');

			    $value .= "<li>";

			    if($catid == 1)
			    {
				    $value .= "<a href='$cathomelink' class='parent'><span>".$category['name']."</span></a>
					    <div><ul>";
			    }
			    else
			    {
				    $value .="<a href='$catmainlink' class='parent'><span>".$category['name']."</span></a>
					    <div><ul>";
			    }
			    if((modApiFunc("Catalog", "hasOnlineSubcategories",$category['id'])) && ($category['level'] > 0))
			    {
				    $value .= $this->defaultsubcatlevel($category['id'],1);	// Looping for subcategories
			    }

			    $value .= "</ul></div></li>";
		    }
	    }
	    return $value;
    }


    function defaultsubcatlevel($catid,$catlevel)
    {
	    $lvl2subcats = modApiFunc("Catalog", "getSubCategories",$catid);
	    // Checking for subcategories which have level greater than 1
	    if($catlevel>1)
	    {
		    $catname = getCategoryName($catid);
		    $catlink = getCategoryLink($catid);
		    $value = "";
		    $value .= "<li><a href='$catlink'><span>".$catname."</span></a><div><ul>";
	    }

	    //Get subcategories for level2 and below
	    $count = count($lvl2subcats)-1;
	    while($count>=0)
	    {
		    $value.= $this->defaultsubcatlevel(getCategoryID($lvl2subcats[$count]["category_id"]),2);
		    $count = $count -1;
	    }

	    if($catlevel>1)
	    {
		    $value .= "</ul></div>";
	    }

	    return $value;
    }

    function getTag($tag)
    {
	    global $application;
	    $value = null;
	    switch ($tag)
	    {
		    case 'MainMenuItems':
			$skin = modApiFunc('Look_Feel', 'getCurrentSkin');
                        $category_menu = false;
                        $category_menu = apply_filters('category_menu_container');
             		if($category_menu === true) {
			    $value = $this->displayCategories();
			}
			else {
			    $value = $this->displayDefaultCategories();
			}
			    break;

		    case 'MobileMainMenuItems':
				$value = $this->displayMobileCategories();
			    break;

		    default:
			    $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
			    break;
	    }
	    return $value;
    }
    var $NoView;
    var $mTmplFiller;
    var $_Template_Contents;
    var $_Menu_Data;
};

?>