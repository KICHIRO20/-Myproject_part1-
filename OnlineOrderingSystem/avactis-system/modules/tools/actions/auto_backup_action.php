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
 *
 * @package Tools
 * @author Alexander Girin
 */
class CreateAutoBackup extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * constructor
     */
    function CreateAutoBackup()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');
        $AutoBackup = $request->getValueByKey('AutoBackup');
        if ($AutoBackup)
        {
			CCacheFactory::clearAll();
            modApiFunc("Session", "set", "AutoBackup", true);
        }
        else
        {
            modApiFunc("Session", "un_set", "AutoBackup");
        }
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**#@-*/

}
?>