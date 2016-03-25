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
 * Action handler on update date/time format.
 *
 * @package Localization
 * @access  public
 * @author Alexander Girin
 */
class UpdateDateTimeFormat extends AjaxAction
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
    function UpdateDateTimeFormat()
    {
    }

    /**
     * @
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        modApiFunc('Localization', 'setValue', "DATE_FORMAT", $request->getValueByKey("date_format"));
        modApiFunc('Localization', 'setValue', "TIME_FORMAT", $request->getValueByKey("time_format"));

        modApiFunc('Session','set','ResultMessage','MSG_DATE_TIME_UPDATED');

        modApiFunc('EventsManager','throwEvent','DateTimeFormatUpdated');

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