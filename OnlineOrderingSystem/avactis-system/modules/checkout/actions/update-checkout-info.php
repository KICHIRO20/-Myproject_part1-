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

class UpdateCheckoutInfo extends AjaxAction
{

    function UpdateCheckoutInfo()
    {
    }

    function saveSettings($attrs)
    {
        modApiFunc('Checkout', 'updateCheckoutFormHash');

    	if (count($attrs)!= 0)
        {
            foreach ($attrs as $i => $a)
            {
                if (is_array($a))
                {
                	$fields = array (
                        "variant_id"    => $attrs['person_info_variant_id'],
                        "attribute_id"  => $a['attr_id'],
                        "visible"       => $a['visible'],
                        "required"      => $a['required'],
                        "name"          => $a['name'],
                        "descr"         => $a['descr']
                    );

                    modApiFunc("Checkout","setPersonInfoFieldList", $fields);
                }
            }
        }
    }

    function onAction()
    {
        global $application;

        $attrs = $_POST;

        foreach ($attrs as $i => $v)
        {
        	if (is_array($v))
        	{
        		if(!array_key_exists('visible',$v))
                {
                	$attrs[$i]['visible'] = 0;
                }
                if(!array_key_exists('required',$v))
                {
                    $attrs[$i]['required'] = 0;
                }

        	    if ($attrs[$i]['visible'] == 0)
                {
                	$attrs[$i]['required'] = 0;
                }
                if ($attrs[$i]['required'] == 1)
                {
                	$attrs[$i]['visible'] = 1;
                }
        	}
        }

        $this->saveSettings($attrs);

        $request = new Request();
        $request->setView('CheckoutInfoList');
        $application->redirect($request);
    }

};

?>