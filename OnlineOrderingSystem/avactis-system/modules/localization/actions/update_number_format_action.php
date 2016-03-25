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
 * Action handler on update number format.
 *
 * @package Localization
 * @access  public
 * @author Alexander Girin
 */
class UpdateNumberFormat extends AjaxAction
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
    function UpdateNumberFormat()
    {
    }

    /**
     * @
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        modApiFunc('Localization', 'setValue', "NUMBER_FORMAT", $request->getValueByKey("digits")."|".
                                                                $request->getValueByKey("decimal_sep")."|".
                                                                $request->getValueByKey("digit_sep")."|");
        modApiFunc('Localization', 'setValue', "NEGATIVE_FORMAT", $request->getValueByKey("negative"));
        modApiFunc('Localization', 'setPattern', '/^\d*\\'.$request->getValueByKey("decimal_sep").'?\d*$/', 'weight');

        modApiFunc('Session','set','ResultMessage','MSG_NUM_FORMAT_UPDATED');

        modApiFunc('EventsManager','throwEvent','NumberFormatUpdated');

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