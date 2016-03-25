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
 * ModuleAdmin module meta info.
 *
 * @package Core
 * @author Alexey Florinsky, Alexander Girin
 * @version $Id$
 */

$moduleInfo = array (
    'name' => 'Users',
    'shortName' => 'USERS',
    'groups'    => 'Main',
    'description' => 'Users module description',
    'version' => '0.1.47700',
    'author' => 'Alexey Florinsky, Alexander Girin',
    'contact' => '',
    'systemModule' => true,
    'mainFile' => 'users_api.php',
    'actions'       => array(
             'AdminZone' => array(
                 'AddAdmin'         => 'add_admin_action.php',
                 'EditAdmin'        => 'edit_admin_action.php',
                 'SetDeleteAdminMembers'  => 'set_delete_admin_member_action.php',
                 'ConfirmDeleteAdmins' => 'confirm_delete_admins_action.php',
                 'PasswordChange'   => 'password_change_action.php',
                 'PasswordUpdate'   => 'password_update_action.php',
                 'PasswordRecovery' => 'password_recovery_action.php',
                 'SignOut'          => 'signout_action.php'
             ),
             'SignIn'           => 'signin_action.php',
             'SetSelectedUser'  => 'set_selected_user_action.php'
                            ),
    'views'         => array(
         'AdminZone'    => array(
             'AdminSignIn'           => 'signin_az.php'
            ,'AdminPasswordUpdate'   => 'password_update_az.php'
            ,'AdminPasswordRecovery' => 'password_recovery_az.php'
            ,'AdminMembers'          => 'admin_members_az.php'
            ,'AdminMemberInfo'       => 'admin_member_info_az.php'
            ,'AdminMemberAdd'        => 'admin_member_add_az.php'
            ,'AdminMemberEdit'       => 'admin_member_edit_az.php'
            ,'AdminMemberDelete'     => 'admin_member_delete_az.php'
            ,'AdminMemberPasswordReset' => 'admin_member_passwd_reset_az.php' //@:
                                ),
         'CustomerZone'    => array(
                                )
                            )
);
?>