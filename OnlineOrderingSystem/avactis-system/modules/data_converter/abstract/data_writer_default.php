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
 *                   DataWriterDefault                          .
 *
 * @package DataConverter
 * @author Oleg F. Vlasenko, Egor V. Derevyankin
 */

loadClass('CWorker');

class DataWriterDefault extends CWorker
{

//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

	function DataWriterDefault()
	{
	}

	/**
	 *               -
	 *
	 * @param array $settings -        settings
	 * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
	 */
	function initWork($settings)
	{
        $this->_process_info['status'] = 'INITED';
	}

 	/**
 	 *                            .
     *
     * @param array $data -                      '<tag name>' => '<tag value>'
 	 * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
 	 */
	function doWork($data)
	{
	}
}



?>