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
 * Store-Setup guide view in admin zone.
 *
 * @package Wizard
 * @author HBWSL
 */
class SetupGuide
{
    function SetupGuide()
	{
        global $application;
		$this->filler = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');
	}

    function output()
    {
		global $application;
/*		$application->registerAttributes(array(
            'Local_Visibility',
		));
*/
		$visibility = modApiFunc('Settings','getParamValue','WIZARD_SETTINGS','STORE_SETUP_GUIDE_HIDE');

		if($visibility==='SHOW')
	        $res = $this->filler->fill("", "container.tpl.html",array());
		else
			$res = '';
        return $res;
	}

/*
    function getTag($tag)
    {
        global $application;

        $value = null;

        switch($tag)
        {
            case 'Local_Visibility':
				$value = modApiFunc('Settings','getParamValue','WIZARD_SETTINGS','STORE_SETUP_GUIDE_HIDE');
                break;
        }
        return $value;
	}
*/
    var $filler;
}