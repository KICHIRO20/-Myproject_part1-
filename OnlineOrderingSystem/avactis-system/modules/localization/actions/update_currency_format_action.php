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
 * Action handler on update currency format.
 *
 * @package Localization
 * @access  public
 * @author Alexander Girin
 */
class UpdateCurrencyFormat extends AjaxAction
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
    function UpdateCurrencyFormat()
    {
    }

    /**
     * @
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $id = $request->getValueByKey("currency");
        modApiFunc('Localization', 'setValue', "CURRENCY_" . $id, $request->getValueByKey("currency")."|".
                                                           $request->getValueByKey("currency_sign"));
        modApiFunc('Localization', 'setCurrencySign', $request->getValueByKey("currency"), $request->getValueByKey("currency_sign"));
        modApiFunc('Localization', 'setValue', "CURRENCY_FORMAT_" . $id, $request->getValueByKey("digits")."|".
                                                                  $request->getValueByKey("decimal_sep")."|".
                                                                  $request->getValueByKey("digit_sep")."|");
        modApiFunc('Localization', 'setValue', "CURRENCY_POSITIVE_FORMAT_" . $id, $request->getValueByKey("positive_currency"));
        modApiFunc('Localization', 'setValue', "CURRENCY_NEGATIVE_FORMAT_" . $id, $request->getValueByKey("negative_currency"));
        modApiFunc('Localization', 'setPattern', '/^\d*\\'.$request->getValueByKey("decimal_sep").'?\d*$/', 'currency_' . $id);

        modApiFunc('Session','set','ResultMessage','MSG_CRNCY_FORMAT_UPDATED');

        modApiFunc('Localization', 'setCurrencyFormatEdited', $id);
        modApiFunc('Localization', 'getSettings');

        modApiFunc('EventsManager','throwEvent','CurrencyFormatUpdated');

//        $request = new Request();
//        $request->setView(CURRENT_REQUEST_URL);
//        $application->redirect($request);
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