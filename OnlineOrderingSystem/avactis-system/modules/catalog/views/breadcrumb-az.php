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
 * Catalog module.
 * Catalog Categories Breadcrumb.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Kolesnikov
 */
class Breadcrumb
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     */
    function Breadcrumb()
    {
        global $application;
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
    }

    /**
     * Outputs references to the category.
     */
    function getLinkToView($cid, $view_name, &$cat_info)
    {
        $_request = new Request();
        $_request->setView  ($view_name);
        $_request->setAction( "SetCurrCat" );
        $_request->setKey   ( "category_id", $cid);
        $res =  $_request->getURL();

        if( $cat_info->getCategoryTagValue('RecursiveStatus') == CATEGORY_STATUS_ONLINE)
        {
        }
        else
        {
            $res = $res . '" style="color: rgb(175, 175, 175);';
        }
        return $res;

        //@ check, whether last parameter was ever used
        //$application->href($_request, -1, -1, 'AdminZone');
    }

    function outputLocationBreadcrumb($parents_list, $links, $view_name = "NavigationBar")
    {
        global $application;

        $CategoryId1Info  = new CCategoryInfo(1);
        $this->CategoryId1Name = $CategoryId1Info->getCategoryTagValue('name');

        $retval = "";
        $isFirst = 1;

        $n = sizeof($parents_list);
        for($i =0 ; $i < $n; $i++)
        {
            $value = $parents_list[$i];

            $cat = new CCategoryInfo($value["id"]);
            $name = prepareHTMLDisplay($value["name"]);
            if( $cat->getCategoryTagValue('RecursiveStatus') == CATEGORY_STATUS_ONLINE)
            {
            }
            else
            {
                $name = '<span style="color: rgb(175, 175, 175);">'.$name.'</span>';
            }

            $arr = array(
                "Href" => $this->getLinkToView($value["id"], $view_name, $cat),
                "Name" => $name,
                "CategoryId1Name" => prepareHTMLDisplay($this->CategoryId1Name)

            );
            if($n == 1)
            {
                $retval .= $this->mTmplFiller->fill("catalog/breadcrumb/", "single.tpl.html", $arr);
            }
            else
            {
                if($i == 0)
                {
                    if ($links)
                    {
                        $retval .= $this->mTmplFiller->fill("catalog/breadcrumb/", "first_link.tpl.html", $arr);
                    }
                    else
                    {
                        $retval .= $this->mTmplFiller->fill("catalog/breadcrumb/", "first.tpl.html", $arr);
                    }
                }
                else
                {
                    if($i == $n-1)
                    {
                        $retval .= $this->mTmplFiller->fill("catalog/breadcrumb/", "last.tpl.html", $arr);
                    }
                    else
                    {
                        if ($links)
                        {
                            $retval .= $this->mTmplFiller->fill("catalog/breadcrumb/", "default_link.tpl.html", $arr);
                        }
                        else
                        {
                            $retval .= $this->mTmplFiller->fill("catalog/breadcrumb/", "default.tpl.html", $arr);
                        }
                    }
                }
            }
        }

        if($retval == "")
        {
           CTrace::wrn(array( "CODE" => "CORE_053"), __CLASS__, __FUNCTION__);
        }

        return $retval;
    }

    /**
     * Returns the Catalog Navigator view.
     *
     * @ finish the functions on this page
     */
    function output($links = true, $view_name = "NavigationBar")
    {
        global $application;
	    $retval = $this->outputLocationBreadcrumb(modApiFunc("Catalog", "getCurrentCategoryFullPath"), $links, $view_name);
        return $retval;
    }

//    /**
//     * Processes tags in the templates for the given view.
//     * @return string tag value, if tag has been processed. NULL, otherwise.
//     */
//    function getTag($tag)
//    {
//        $value = null;
//    	switch ($tag)
//    	{
//    	    case 'Items':
//    	        $value = $this->outputCategoriesBreadCrumb();
//    	        break;
//    		case 'CategoryName':
//    			$value = $this->_Current_Category['name'];
//    			break;
//    		case 'CategoryLink':
//    			$value = $this->getLinkToCatalogProdList($this->_Current_Category['id']);
//    			break;
//
//    		default:
//    			break;
//    	}
//    	return $value;
//    }
//
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $_Current_Category;

    /**
     * Reference to the object TemplateFiller.
     *
     * @var TemplateFiller
     */
    var $mTmplFiller;

    /**
     * Name of categy with id = 1.
     * Root category.
     */

    var $CategoryId1Name;
    /**#@-*/

}
?>