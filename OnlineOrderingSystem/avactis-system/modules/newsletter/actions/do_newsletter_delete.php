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
 * @package Newsletter
 * @author Egor Makarov
 */
class do_newsletter_delete extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * do_newsletter_save constructor
     */
    function do_newsletter_delete()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

        $request = new Request();
        $letter_id = $request->getValueByKey('letter_id');
        unset($_POST['letter_id']);
        unset($_POST['asc_action']);
        modApiFunc('Newsletter', 'deleteMessages', array($letter_id));
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