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

class del_values_of_color_swatch_action extends AjaxAction
{
    function del_values_of_color_swatch_action()
    {
    }

    function onAction()
    {
		global $application;

		$Colorswatchdelinfo = array();

		// copy all the inputted data to the array $SessionPost
		$Colorswatchdelinfo = $_REQUEST;

		// create an empty array of errors.
		$Colorswatchdelinfo["ViewState"]["ErrorsArray"] = array();
		$prodid = $Colorswatchdelinfo['product_id'];

		modApiFunc('ColorSwatch', 'deleteColorSwatchInfo',$Colorswatchdelinfo);

		 $request = new Request();

        $request->setView('PI_ColorSwatch');
	$request->setKey('product_id',$prodid);
         modApiFunc('Session','set','DeleteMsg',1);
        $application->redirect($request);

	}

}

?>