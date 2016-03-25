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
 * @package Taxes
 * @author Alexander Girin
 */
class DeleteProdTaxClass extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DeleteProdTaxClass constructor.
     */
    function DeleteProdTaxClass()
    {
    }

    /**
     * Deletes Product Tax Class.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        modApiFunc('Taxes', 'deleteProdTaxClass', ($request->getValueByKey('ptc_id'))? $request->getValueByKey('ptc_id'):$request->getValueByKey('TaxId'));
        $request = new Request();
        $request->setView('TaxSettings');
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