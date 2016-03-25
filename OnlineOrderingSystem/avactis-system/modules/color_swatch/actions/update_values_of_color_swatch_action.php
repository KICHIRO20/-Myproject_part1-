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

class update_values_of_color_swatch_action extends AjaxAction
{
    function update_values_of_color_swatch_action()
    {
    }

    function onAction()
    {
		global $application;

		$ColorswatchEditinfo = array();

		// copy all the inputted data to the array $SessionPost
		$ColorswatchEditinfo = $_REQUEST;

		// create an empty array of errors.
		$ColorswatchEditinfo["ViewState"]["ErrorsArray"] = array();
		$prodid = $ColorswatchEditinfo['product_id'];

		modApiFunc('ColorSwatch', 'updateColorSwatchInfo',$ColorswatchEditinfo);
               // modApiFunc('ColorSwatch', 'updateNumberAndLabel',$ColorswatchEditinfo);

		$request = new Request();

        $request->setView('PI_ColorSwatch');
	$request->setKey('product_id',$prodid);
        modApiFunc('Session','set','SavedMsg',1);
        $application->redirect($request);

	}

}

?>