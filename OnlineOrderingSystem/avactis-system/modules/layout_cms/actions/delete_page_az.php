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
 * @package Layout CMS
 * @author Alexey Astafyev
 *
 */

class delete_page extends AjaxAction
{
    function delete_page()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $page = $request->getValueByKey('page');

        return modApiFunc('Layout_CMS', 'deletePage', $page);
    }
}