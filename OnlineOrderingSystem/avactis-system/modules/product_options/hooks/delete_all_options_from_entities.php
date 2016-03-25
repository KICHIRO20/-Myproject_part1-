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

class DeleteAllOptionsFromEntities
{
    function DeleteAllOptionsFromEntities()
    {
    }

    function onHook($actionObj)
    {
        switch(_ml_strtolower(get_class($actionObj)))
        {
            case "confirmdeleteproducts":
                    $parent_entity = "product";
                    $entities_ids = modApiFunc("Catalog","getEditableProductsID");
                    break;
            case "confirmdeletecategory":
                    global $application;
                    $request = &$application->getInstance('Request');
                    $parent_entity = "product";
                    $entities_ids = explode("|", $request->getValueByKey('ProdsId'));
                    break;
            case "confirmdeleteproducttypes":
                    global $application;
                    $request = &$application->getInstance('Request');
                    $parent_entity = "ptype";
                    $entities_ids = $request->getValueByKey('ProductType');
                    break;
        }

        modApiFunc("Product_Options","delAllOptionsFromEntities",$parent_entity,$entities_ids);
        modApiFunc("Product_Options","delAllCRulesFromEntities",$parent_entity,$entities_ids);
        modApiFunc("Product_Options","delAllInventoryFromEntities",$parent_entity,$entities_ids);
        modApiFunc("Product_Options","delAllOptionsSettingsFromEntities",$parent_entity,$entities_ids);
    }

};

?>