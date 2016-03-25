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
 * @package Catalog
 * @author Sergey Kulitsky
 *
 */

class SetProductGroup extends AjaxAction
{
    function SetProductGroup()
    {
    }

    function onAction()
    {
        global $application;

        $request = &$application -> getInstance('Request');
        $prod_ids = $request -> getValueByKey('selected_products');

        // saving the $prod_ids array in session for the group edit form
        modApiFunc('Session', 'set', 'PGE_PRODUCTS', $prod_ids);

        // clearing past data if any
        if (modApiFunc('Session', 'is_set', 'PGE_ERRORS'))
            modApiFunc('Session', 'un_set', 'PGE_ERRORS');
        if (modApiFunc('Session', 'is_set', 'PGE_POSTED_DATA'))
            modApiFunc('Session', 'un_set', 'PGE_POSTED_DATA');

        // final redirect to product group edit form
        $redirect = new Request();
        $redirect -> setView('CatalogProductGroupEdit');

        $application -> redirect($redirect);
    }
}