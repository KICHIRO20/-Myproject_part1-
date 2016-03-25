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
 * News module.
 * Action handler on UpdateNewsSettings.
 *
 * @package News
 * @author Timur Nasibullin
 */
class UpdateNewsSettings extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * UpdateNewsSettings constructor.
     */
    function UpdateNewsSettings()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

        $request = $application->getInstance('Request');
        $news_display_count = $request->getValueByKey(NEWS_DISPLAY_COUNT);
        $values = array(
            NEWS_DISPLAY_COUNT     => $news_display_count
        );
        foreach($values as $key => $value)
        {
            modApiFunc('News','setValue',$key,$value);
        };
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