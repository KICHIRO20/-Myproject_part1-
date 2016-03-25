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
class SetDeleteAdminMembers extends AjaxAction
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
    function SetDeleteAdminMembers()
    {
    }

    /**
     *
     *
     * Action:
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $selected_admins = $request->getValueByKey('selected_admins');

        //         .                                          -                                  .
        //                                                 -               Delete
        $removable_admins = array();
        $unremovable_admins = array();

        foreach($selected_admins as $index => $admin_id)
        {
        	if(!is_numeric($admin_id) || (((int)$admin_id) == 0))
                continue;
        	else
        	    $admin_id = (int)$admin_id;

            $results_array = modApiFunc('EventsManager','processEvent','RemoveAdmin', $admin_id);
            $res_msg = "";
            foreach($results_array as $msg)
            {
                if($msg !== NULL)
                {
                    //    -                          .                                       .
                    if(_ml_strpos($res_msg, $msg) === FALSE)
                    {
                        $res_msg .= $msg . "\n";
                    }
                }
            }
            if($res_msg == "")
            {
                //                      -              .
                $removable_admins[] = $admin_id;
            }
            else
            {
                //          -        .                                             .
                $unremovable_admins[] = array
                (
                    "id"  => $admin_id
                   ,"msg" => $res_msg
                );
            }
        }

        $res = array
        (
            "removable_admins" => $removable_admins,
            "unremovable_admins" => $unremovable_admins
        );
        modApiFunc("Users", "setDeleteAdminMembersID", $res);
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