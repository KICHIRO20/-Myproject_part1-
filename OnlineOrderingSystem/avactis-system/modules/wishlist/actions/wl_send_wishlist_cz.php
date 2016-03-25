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
 * @package Wishlist
 * @author Sergey Kulitsky
 */
class SendWishlist extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * UpdateWishlistContent constructor.
     */
    function SendWishlist()
    {
    }

    /**
     * Updates the cart contents.
     */
    function onAction()
    {
        global $application;
        $request = $application -> getInstance('Request');

        if (!modApiFunc('Customer_Account', 'getCurrentSignedCustomer'))
            return;

        $email = trim($request -> getValueByKey('wl_send_email'));

        $validator = &$application -> getInstance('CAValidator');
        if ($validator -> isValid('email', $email))
        {
            loadCoreFile('ascHtmlMimeMail.php');
            $mail = new ascHtmlMimeMail();
            $mail -> setText(cutTemplatesPathes(getSendWishlistContent()));
            $mail -> setSubject(getMsg('CZ', 'WL_SEND_WISHLIST_SUBJECT'));
            $mail -> setFrom(modApiFunc('Notifications', 'getExtendedEmail', '', 'EMAIL_STORE_OWNER', true));
            $mail -> send(array($email));
            modApiFunc('Session', 'set', 'WishlistResultMessage',
                       getMsg('CZ', 'WL_WISHLIST_SENT'));
        }
        else
        {
            modApiFunc('Session', 'set', 'WishlistErrorMessage',
                       getMsg('CZ', 'WL_INCORRECT_EMAIL_SPECIFIED'));
        }

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
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