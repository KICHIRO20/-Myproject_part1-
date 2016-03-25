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
 *
 * @package TaxRateByZip
 * @author Ravil Garafutdinov
 */
class TaxRatesRedirectToImportAction extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * RedirectToImportAction constructor.
     */
    function TaxRatesRedirectToImportAction()
    {
    }

    /**
     * Deletes tax rate by zip set.
     */
    function onAction()
    {
        global $application;

        $SessionPost = array();

        $SessionPost = $_POST;
        $Errors = array();
        $Result = array();

        $request = new Request();
        $request->setView("PopupWindow");

        $updateSid = $request->getValueByKey("updateSid", 0);
        if ($updateSid)
        {
            $request->setKey("updateSid", $updateSid);
        }

        $description = '';
        if (isset($_POST['file_description']))
        {
            $description = prepareHTMLDisplay(trim($_POST['file_description']));
        }

        if ($description == '')
        {
            $SessionPost['Errors'][] = getMsg("TAX_ZIP", "ADD_NEW_SET_EMPTY_FILE_DESCRIPTION_ERROR");
            modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

            $request->setKey("page_view", "TaxRateByZip_AddNewSet");
            $application->redirect($request);

            return;
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        $sid = modApiFunc("TaxRateByZip", "addSetToDB", $description, $_POST["csv_file_name"]);

        $request->setKey("page_view", "TaxRatesImportView");
        $request->setKey("sid", $sid);
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