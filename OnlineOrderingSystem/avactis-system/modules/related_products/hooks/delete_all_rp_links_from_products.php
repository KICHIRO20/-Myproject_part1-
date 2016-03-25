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
 * @package RelatedProducts
 * @author Egor V. Derevyankin
 *
 */

class DeleteAllRPLinksFromProducts
{
    function DeleteAllRPLinksFromProducts()
    {}

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
                    $products_ids = array_filter(explode("|", $request->getValueByKey('ProdsId')));
                    break;
        };

        modApiFunc('Related_Products','OnProductsWereDeleted',$products_ids);
    }
};

?>