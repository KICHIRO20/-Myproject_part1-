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
 * @package Shipping Cost Calculator
 * @access  public
 * @author Ravil Garafutdinov
 */
class DeleteFsRule extends AjaxAction
{

    function DeleteFsRule()
    {
    }

    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = $_POST;

        $FsRule_id = $request->getValueByKey('delete_fs_rule_id');

        if(!empty($FsRule_id))
        {
            modApiFunc("Shipping_Cost_Calculator", "deleteFsRuleByIdsArray", $FsRule_id);
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
    }

};

?>