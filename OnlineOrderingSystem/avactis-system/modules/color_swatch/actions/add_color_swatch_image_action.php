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
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by Pentasoft Corp.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2010, Pentasoft Corp.
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

class add_color_swatch_image_action extends AjaxAction
{
    function add_color_swatch_image_action()
    {
    }

    function onAction()
    {
		global $application;

		$Colorswatchinfo = array();

		// copy all the inputted data to the array $SessionPost
		$Colorswatchinfo = $_REQUEST;

		// create an empty array of errors.
		$Colorswatchinfo["ViewState"]["ErrorsArray"] = array();
		$prodid = $Colorswatchinfo['product_id'];

		modApiFunc('ColorSwatch', 'addColorSwatchInfo',$Colorswatchinfo);

		 $request = new Request();

        $request->setView('PI_ColorSwatch');
	$request->setKey('product_id',$prodid);
        modApiFunc('Session','set','ResultMessage',1);
        $application->redirect($request);

	}

}

?>