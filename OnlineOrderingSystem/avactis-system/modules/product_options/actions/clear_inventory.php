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

class clear_inventory extends AjaxAction
{
    function clear_inventory()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $parent_entity = $request->getValueByKey('parent_entity');
        $entity_id = $request->getValueByKey('entity_id');

        modApiFunc("Product_Options","__clearITForEntity",$parent_entity,$entity_id);
    }
};

?>