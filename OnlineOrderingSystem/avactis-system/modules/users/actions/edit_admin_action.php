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

_use(dirname(__FILE__).'/add_admin_action.php');

/**
 * Users module.
 *
 * @package Users
 * @author Alexandr Girin
 * @access  public
 */
class EditAdmin extends AddAdmin
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
    function EditAdmin()
    {
        global $application;
        $request = new Request();
        $request->setView('AdminMemberEdit');
    }

    function saveDataToDB($data)
    {
        if(empty($data["Options"])) $data["Options"] = array();
        modApiFunc("Users", "updateAdmin",
                   $data['id'],
                   $data["FirstName"],
                   $data["LastName"],
                   $data["Email"],
                   $data["Options"]
                  );
        modApiFunc('Users', 'setAdminPermissions', $data['id'], $data['Permissions']);
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