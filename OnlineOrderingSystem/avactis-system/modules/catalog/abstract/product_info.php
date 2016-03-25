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

class Catalog_ProdInfo_Base {

    /**
     * @ describe the function Catalog_ProdInfo_Base->.
     */
    function getProductInfo($prod_id = 0)
    {
        global $application;
        $catalog = &$application->getInstance('Catalog');

        if ($prod_id)
            $this->prod_id = $prod_id;
        else
            $this->prod_id = $catalog->getCurrentProductID();

        $this->product_info = modApiFunc('Catalog', 'getProductInfo', $this->prod_id);

        $arr = array();
        if (is_array($this->product_info))
            foreach ($this->product_info['attributes'] as $attr)
            {
                if (!is_array($attr))
                {
                    continue;
                }
                //                                    ,      PriceExcludingTaxes.
                if(isset($attr['group']))
                {
                    $grp_id = $attr['group']['id'];
                    if (!key_exists($grp_id, $arr))
                    {
                        $arr[$grp_id] = $attr['group'];
                    }
                    $arr[$grp_id]['attr'][] = $attr;
                }
            }

        usort($arr, array("Catalog_ProdInfo_Base", "cmp"));
        foreach ($arr as $key => $attr)
        {
        	usort($attr['attr'], array("Catalog_ProdInfo_Base", "cmp"));
        	$arr[$key] = $attr;
        }

        // delete the empty groups, which have no visible attributes
        foreach ($arr as $group_id => $group)
        {
            $visible = false;
        	foreach ($group['attr'] as $attr_id => $attr)
        	{
                //                                    ,      PriceExcludingTaxes.
                if(isset($attr['visible']))
                {
            		if ($attr['visible'])
            		{
            		    $visible = true;
            		}
                }
        	}
        	if (!$visible)
        	{
        		unset($arr[$group_id]);
        	}
        }

        return $arr;
    }

    /**
     * Returned the product type description.
     */
    function getProductType()
    {
        if(!modApiFunc('Catalog', 'isCorrectProductTypeId', $this->product_type_id))
            $this->product_type_id = 1;

    	$product_type = modApiFunc('Catalog', 'getProductType', $this->product_type_id);
    	$this->product_type = $product_type['attr'];

        $arr = array();
        foreach ($this->product_type as $view_tag => $attr)
        {
        	$grp_id = $attr['group']['id'];
        	if (!key_exists($grp_id, $arr))
        	{
        		$arr[$grp_id] = $attr['group'];
        	}
        	$arr[$grp_id]['attr'][] = $attr;
        }

        usort($arr, array("Catalog_ProdInfo_Base", "cmp"));
        foreach ($arr as $key => $attr)
        {
        	usort($attr['attr'], array("Catalog_ProdInfo_Base", "cmp"));
        	$arr[$key] = $attr;
        }

        return $arr;
    }

    function cmp($a, $b)
    {
        if(!isset($a['sort']) ||
           !isset($a['sort']))
           //                                    ,      PriceExcludingTaxes.
           //    CZ                           .
           return 1;
        if ($a['sort'] == $b['sort']) {
        	return 0;
        }
        return ($a['sort'] < $b['sort']) ? -1 : 1;
    }


    var $product_info;
    var $product_type;
    var $product_type_id;
    var $prod_id;
}
?>