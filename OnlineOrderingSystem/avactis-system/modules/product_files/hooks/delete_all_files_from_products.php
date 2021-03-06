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
 * @package ProductFiles
 * @author Egor V. Derevyankin
 *
 */

class DeleteAllFilesFromProducts
{
    function DeleteAllFilesFromProducts()
    {
    }

    function onHook($actionObj)
    {
        switch(_ml_strtolower(get_class($actionObj)))
        {
            case "confirmdeleteproducts":
                    $products_ids = modApiFunc("Catalog","getEditableProductsID");
                    break;
            case "confirmdeletecategory":
                    global $application;
                    $request = &$application->getInstance('Request');
                    $products_ids = explode("|", $request->getValueByKey('ProdsId'));
                    break;
        };

        modApiFunc('Product_Files','delAllFilesFromProducts',$products_ids);
    }
};

?>