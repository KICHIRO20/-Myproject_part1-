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

class SetCurrentProductType extends AjaxAction
{
    /**
     * @ describe the function SetCurrentProductType->.
     */
    function onAction()
    {
        $type_id = modApiFunc('Request', 'getValueByKey', 'type_id');
        if (!$type_id)
        {
        	$type_id = 1;
        }
    	modApiFunc('Catalog', 'setCurrentProductTypeID', $type_id);
    }
}

?>