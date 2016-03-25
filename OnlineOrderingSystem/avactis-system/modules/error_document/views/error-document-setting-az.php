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
 * Error_Document module.
 *
 * @package Error_Document
 * @author HBWSL
 */
class Error_Document_Setting
{
    function Error_Document_Setting()
    {
        global $application;
		$this->filler = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');
    }

	function output()
	{
		global $application;
		$application->registerAttributes(array(
            'Local_Is_Active',
		));

        $res = $this->filler->fill("", "container.tpl.html",array());
        return $res;
	}

    function getTag($tag)
    {
        global $application;

        $value = null;

        switch($tag)
        {
            case 'Local_Is_Active':
				$value = modApiFunc('Error_Document','isActive');
                break;
        }
        return $value;
	}

    var $filler;
}