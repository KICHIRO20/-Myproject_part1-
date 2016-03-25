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
 * @package ProductOptions
 * @author Egor V. Derevyankin
 *
 */

class CopyAllOptionsFromEntityToEntity
{
    function CopyAllOptionsFromEntityToEntity()
    {
    }

    function onHook($actionObj)
    {
        switch(_ml_strtolower(get_class($actionObj)))
        {
            case "copytoproducts":
                    $from_entity = "product";
                    $to_entity = "product";
                    $copy_results = modApiFunc("Session","get","CopyProductsResult");
                    break;
            case "addproductinfoaction":
                    if($actionObj->new_product_id==null)
                        return;
                    $from_entity = "ptype";
                    $to_entity = "product";
                    $copy_results = array($actionObj->ptype_id_of_new_product => $actionObj->new_product_id);
                    break;
        }

        if(!empty($copy_results))
            foreach($copy_results as $old_eid => $new_eid)
            {
                $tmap = modApiFunc("Product_Options","copyAllOptionsFromEntityToEntity",$from_entity,$old_eid,$to_entity,$new_eid);
                modApiFunc("Product_Options","copyAllOptionsSettingsFromEntityToEntity",$from_entity,$old_eid,$to_entity,$new_eid);
                modApiFunc("Product_Options","copyAllCRulesFromEntityToEntity",$from_entity,$old_eid,$to_entity,$new_eid,$tmap);
                modApiFunc("Product_Options","copyAllInventoryFromEntityToEntity",$from_entity,$old_eid,$to_entity,$new_eid,$tmap);
            }
    }
}

?>