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
 * @package Manufacturers
 * @access  public
 */
class set_editable_manufacturer extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor
     *
     * @ finish the functions on this page
     */
    function set_editable_manufacturer()
    {
    }


    /**
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $mnf_id = $request->getValueByKey( 'manufacturer_id' );

        if ($mnf_id != NULL)
        {
            modApiFunc('Manufacturers', 'setEditableManufacturerID', $mnf_id);
        }
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