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

_use(dirname(__FILE__).'/add_manufacturer.php');

/**
 * @access  public
 * @author  Vadim Lyalikov
 */
class update_manufacturer extends add_manufacturer
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor
     */
    function update_manufacturer()
    {
    }

    function saveDataToDB($data)
    {
        modApiFunc("Manufacturers", "updateManufacturer",
                   $data["ManufacturerID"],
                   $data["ManufacturerImage"]->get_id(),
                   $data["ManufacturerName"],
                   $data["ManufacturerUrl"],
                   $data["ManufacturerDesc"],
                   $data["ManufacturerStatus"]
                  );
     }

    /**
     * Redirect after action
     */
    function redirect()
    {
        global $application;
        $request = new Request();
        $request->setView('EditManufacturer');
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