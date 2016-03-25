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
 * Users module.
 *
 * @package Users
 * @author Alexandr Girin
 * @access  public
 */
class SetSelectedUser extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     *
     * @ finish the functions on this page
     */
    function SetSelectedUser()
    {
    }

    /**
     * Sets a current inventory product from Request.
     *
     * Action: setCurrCat
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $user_id = $request->getValueByKey('uid');
        modApiFunc("Users", "setSelectedUserId", $user_id);
        $edit = $request->getValueByKey('edit');
        modApiFunc("Users", "setShowEditButtonInInfo", $edit? true:false);
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