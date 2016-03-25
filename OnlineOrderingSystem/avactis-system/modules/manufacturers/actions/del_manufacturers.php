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
 * @author Vadim Lyalikov
 *
 */

class del_manufacturers extends AjaxAction
{
    function del_manufacturers()
    {
    }

    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;

        $Manufacturer_id = $request->getValueByKey('manufacturer_id');
        if(!empty($Manufacturer_id) &&
           ctype_digit($Manufacturer_id) === TRUE)
        {
            $this->mnf_ids = array($Manufacturer_id);
        	modApiFunc("Manufacturers", "delManufacturers", array($Manufacturer_id));
        }
        else
        {
            $this->mnf_ids = NULL;
        }

        // get view name by action name.
        $this->redirect();
    }

    /**
     * Redirect after action
     */
    function redirect()
    {
        global $application;

        $request = new Request();
        $request->setView('ManufacturersList');
        $application->redirect($request);
    }
    };

?>