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
class ConfirmDeleteAdmins extends AjaxAction
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
    function ConfirmDeleteAdmins()
    {
    }

    /**
     * Sets a current inventory product from Request.
     *
     * Action: setCurrCat.
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        $SessionPost = $_POST;
        $SessionPost["ViewState"]["hasCloseScript"] = true;
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $data = modApiFunc("Users", "getDeleteAdminMembersID");
        $array_id = $data['removable_admins'];
        if(!empty($array_id))
        {
            modApiFunc("Users", "deleteAdmins", $array_id);
        }
        modApiFunc("Users", "unsetDeleteAdminMembersID", $array_id);
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