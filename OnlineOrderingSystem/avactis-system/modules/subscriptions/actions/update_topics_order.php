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
 *
 * @package Subscriptions
 * @access  public
 */
class update_topics_order extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**
     * @access public
     */

    function update_topics_order()
    {
    }

    /**
     *
     *
     * Action: .
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = $_POST;

        $topicsSortOrder = $request->getValueByKey( 'ObjectList_hidden' );
        $topicsSortOrderArray = explode('|', $topicsSortOrder);

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        if ($topicsSortOrderArray != NULL) {
            modApiFunc('Subscriptions', 'setTopicsSortOrder', $topicsSortOrderArray);
        }
    }

    /**-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**+
     * @access private
     */

    /**-*/

}

?>