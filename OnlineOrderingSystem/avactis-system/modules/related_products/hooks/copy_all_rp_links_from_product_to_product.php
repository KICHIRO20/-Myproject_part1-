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

class CopyAllRPLinksFromProductToProduct
{
    function CopyAllRPLinksFromProductToProduct()
    {}

    function onHook($actionObj)
    {
        switch(_ml_strtolower(get_class($actionObj)))
        {
            case "copytoproducts":
                    $copy_results = modApiFunc("Session","get","CopyProductsResult");
                    break;
        };

        if(!empty($copy_results))
        {
            foreach($copy_results as $old_pid => $new_pid)
            {
                modApiFunc("Related_Products","copyAllRPLinksFromProductToProduct",$old_pid,$new_pid);
            };
        };
    }
};

?>