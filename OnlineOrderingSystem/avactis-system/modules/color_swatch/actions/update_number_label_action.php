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

class update_number_label_action extends AjaxAction
{
    function update_number_label_action()
    {
    }

    function onAction()
    {
		global $application;

		$ColorswatchEditval = array();

		// copy all the inputted data to the array $SessionPost
		$ColorswatchEditval = $_REQUEST;

		// create an empty array of errors.
		$ColorswatchEditval["ViewState"]["ErrorsArray"] = array();
		$prodid = $ColorswatchEditval['product_id'];

                modApiFunc('ColorSwatch', 'updateNumberAndLabel',$ColorswatchEditval);

		$request = new Request();

        $request->setView('PI_ColorSwatch');
	$request->setKey('product_id',$prodid);
        modApiFunc('Session','set','SavedMsg',1);
        $application->redirect($request);

	}

}

?>